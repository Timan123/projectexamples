USE [BillingSystem]
GO
/****** Object:  StoredProcedure [dbo].[BE_LoadOrderfromSD]    Script Date: 4/19/2016 10:50:42 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO





/*This grabs all the OPM orders and loads them into the billing engine*/

SELECT OrderID
INTO #neworders_OPM
from tlg.dbo.[V_SDScoreCard] sd 
join BillingSystem.dbo.Ipv4Billing i on sd.OrderId = i.CreatedOrder
where 
sd.ProvOrderStatus = 'Completed' 
and 
sd.BillingEntity = 'US'
and
sd.OrderClassInt != 2
and 
(sd.CustomerName not like 'Cogen%' or sd.CustomerName = 'Cogent Partners')
and
sd.OrderID not in (select oh_OrderID from Billingsystem.dbo.orderheaders)
and 
sd.OrderID not in (select od_OrderID from Billingsystem.dbo.orderdetails)
and sd.billstartdt is not null --added 2/10/09 by DG to offset issue with SD switching status to complete and entering billdate days later

--REMOVE BELOW ANDS FOR PRODUCTION
--and sd.Region = 'US'


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
where OPMLI.IsActive = 1




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

FROM
          #orderlineitems_OPM TOL 
          
	  JOIN tlg.mjain.orderscheduledates d	ON TOL.OrderID = d.OrderID
	
order by TOL.OrderID



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
SELECT
	'oh_OrderId' 			= 	rtrim(ltrim(sd.OrderID)),
	'oh_referenceid'		= 	NULL,
	'oh_OrderStatus' 		= 	5,
	'oh_CustomerID' 		= 	rtrim(ltrim(Ic.GPCustomerID)),
	'oh_ServiceAcceptance' 	= 	d.CustomerAccptDt,
	'oh_Cycle' 				= 	NULL,
	'oh_BillingBatch'		=   '1',
	'oh_BillingEntity'		=	'3', --US OPMBillingEntity
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
	

FROM 	tlg.dbo.[V_SDScoreCard] sd
	JOIN tlg.mjain.OPM_Order_Details ood on sd.Orderid = ood.OrderID
    JOIN tlg.mjain.orderscheduledates d on sd.OrderID = d.OrderID
	JOIN tlg.mjain.OPM_Order_Term oot on ood.Term = oot.TermCode
	JOIN tlg.dbo.Integration_Customermm_Order_Billing IC ON sd.OrderID = Ic.CustomerOrderID 

WHERE sd.OrderID in (select od_orderid from orderdetails) and
	sd.OrderID not in (select oh_orderid from orderheaders)

delete from orderdetails where od_orderid not in (select oh_orderid from orderheaders)

drop table  #neworders_OPM
drop table  #orderlineitems_OPM


;