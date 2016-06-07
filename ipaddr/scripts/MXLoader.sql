USE [BillingSystem]
GO
/****** Object:  StoredProcedure [dbo].[BE_LoadOrderfromSD_MX]    Script Date: 4/19/2016 11:14:23 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


/*This grabs all the MX OPM orders and loads them into the billing engine*/

SELECT OrderID
INTO #neworders_OPM
from tlg.dbo.[V_SDScoreCard] sd 
join BillingSystem.dbo.Ipv4Billing i on sd.OrderId = i.CreatedOrder
where 
sd.ProvOrderStatus = 'Completed' 
and 
sd.BillingEntity = 'Mexico'
and
sd.OrderClassInt != 2
and 
sd.CustomerName not like 'Cogen%'
and
sd.OrderCancelDt is null
and
sd.OrderID not in (select oh_OrderID from Billingsystem.dbo.orderheaders)
and 
sd.OrderID not in (select od_OrderID from Billingsystem.dbo.orderdetails)
and sd.billstartdt is not null 


SELECT 

	'OrderID' 				= 	#neworders_OPM.OrderID,
	'ProductCode' 			= 	V.[Price List],
	'OrderLineItemIndexNum'	=	OPMLI.TableIndexNum,
	'LineItemDesc'			=	OLI.LineItemDesc,
	'LineItemType'			=	OLI.LineItemType,
	'GPLineItem'			=	OLI.GPLineItem,
	'Quantity' 				=	'1',

	'Price'	 				= 	
	CASE
	WHEN ood.ConversionToEuro is not null THEN (OPMLI.DiscountPrice * ood.ConversionToEuro)
	ELSE OPMLI.DiscountPrice
	END


INTO #orderlineitems_OPM

FROM #neworders_OPM
join tlg.dbo.V_SiebelOrders V on #neworders_OPM.OrderID = V.ORDER_NUM
join tlg.mjain.OPM_Order_Details OOD on V.ORDER_NUM = OOD.OrderID
join tlg.dbo.OPMOrderLineItems OPMLI on OOD.OrderID = OPMLI.OrderID
join tlg.dbo.OPMLineItems OLI on OPMLI.LineItemID = OLI.ItemIndexNum
where 
OLI.LineItemType not in (3,4) and
OPMLI.IsActive = 1





SELECT 			

	'od_OrderID' 		= 	rtrim(ltrim(TOL.OrderID)),
	'od_ItemID' 		= 	rtrim(ltrim(TOL.GPLineItem)),
	'od_Description' 	=  	'IPV4 ON',
	'od_Start'		=	d.StartBillingDt,
	'od_End' 		= 	NULL,
	'od_Quantity' 		= 	TOL.Quantity,
	'od_UnitPrice' 		= 	TOL.Price,
	'od_ExtendedAmount'	=	TOL.Price,
	'od_Imported' 		= 	Getdate(),
	'od_Lastbilled'		= 	DATEADD(MONTH, DATEDIFF(MONTH, -1, d.StartBillingDt)-1, -1) --last day of previous month

INTO #orderdetails_temp

FROM
          #orderlineitems_OPM TOL 

  	  JOIN tlg.mjain.orderscheduledates d	ON TOL.OrderID = d.OrderID
	
order by TOL.OrderID

INSERT INTO billingsystem..orderdetails	
	(
	[od_OrderID],
	[od_ItemID],
	[od_Description],
	[od_Start],
	[od_End],
	[od_Quantity],
	[od_UnitPrice],
	[od_ExtendedAmount],
	[od_Imported],
	[od_LastBilled]
	) 
select * from #orderdetails_temp





SELECT
	'oh_OrderId' 			= 	rtrim(ltrim(sd.OrderID)),
	'oh_referenceid'		= 	NULL,
	'oh_OrderStatus' 		= 	5,
	'oh_CustomerID' 		= 	rtrim(ltrim(Ic.GPCustomerID)),
	'oh_ServiceAcceptance' 	= 	d.CustomerAccptDt,
	'oh_Cycle' 				= 	NULL,
	'oh_BillingBatch'		=   6, --Mexico billing batch #
	'oh_BillingEntity'		=	61, --canadia OPMbilling entity
	'oh_ExchangeRateToEUR'	=	ood.ConversionToEuro,
	'oh_Frequency' 			= 	'Monthly',
	'oh_Terms' 				= 	rtrim(ltrim(oot.TermDesc)),
	'oh_OrderType' 			= 	'COI',
	'oh_Pricelist' 			= 	rtrim(ltrim(sd.ProductCode)),
	'oh_Start' 				= 	d.StartBillingDt,
	'oh_End'	 			=  	null,
	'oh_Imported' 			= 	GetDate(),
	'oh_LastBilled' 		= 	DATEADD(MONTH, DATEDIFF(MONTH, -1, d.StartBillingDt)-1, -1), --last day of previous month
	'oh_NextBill' 			= 	NULL,
	'oh_terminationdate'	= 	NULL

INTO #orderheaders_temp	

FROM 	 tlg.dbo.[V_SDScoreCard] sd
	JOIN tlg.mjain.OPM_Order_Details ood on sd.Orderid = ood.OrderID
    JOIN tlg.mjain.orderscheduledates d on sd.OrderID = d.OrderID
	JOIN tlg.mjain.OPM_Order_Term oot on ood.Term = oot.TermCode
	JOIN tlg.dbo.Integration_Customermm_Order_Billing_MX IC ON sd.OrderID = Ic.CustomerOrderID 
--updated Integration_CustomerMM to reflect _CA for CA orders 15-aug-07

WHERE sd.OrderID in (select od_orderid from orderdetails) and
	sd.OrderID not in (select oh_orderid from orderheaders)

INSERT INTO BillingSystem..OrderHeaders
(
	oh_OrderId 		,
	oh_referenceid	,
	oh_OrderStatus 	,
	oh_CustomerID 	,
	oh_ServiceAcceptance 	,
	oh_Cycle 		,
	oh_BillingBatch ,
	oh_BillingEntity,
	oh_ExchangeRateToEUR,
	oh_Frequency 		,
	oh_Terms 		,
	oh_OrderType 		,
	oh_Pricelist 		,
	oh_Start 		,
	oh_End	 	,
	oh_Imported 		,
	oh_LastBilled 	,
	oh_NextBill 		,
	oh_terminationdate	
) 
select * FROM  #orderheaders_temp	


delete from orderdetails where od_orderid not in (select oh_orderid from orderheaders)
--select * from orderdetails where od_orderid not in (select oh_orderid from orderheaders)

drop table  #neworders_OPM
drop table  #orderlineitems_OPM
drop table #orderdetails_temp
drop table #orderheaders_temp

--select * from #neworders_OPM
--select * from #orderlineitems_OPM


;