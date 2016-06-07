<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageController
 *
 * @author tcassidy
 */
class PageController  extends \BaseController
{
	
	public function dashboardPage()
	{
		return $this->view('dashboard', []);
	}
	
	public function individualPage()
	{
		return $this->view('individual', []);
	}
	
	public function sowPage()
	{
		
		$by = Input::get('by');
		$type    = Input::get('type');
		$q  = Input::get('q');

		return $this->view('SOW', compact( 'by', 'type', 'q'));
	}
	
	public function provPage()
	{
		
		$by = Input::get('by');
		$type    = Input::get('type');
		$q  = Input::get('q');

		return $this->view('prov', compact( 'by', 'type', 'q'));
	}
	
	public function buildingPage()
	{
		
		$by = Input::get('by');
		$type    = Input::get('type');
		$q  = Input::get('q');

		return $this->view('building', compact( 'by', 'type', 'q'));
	}
	
	public function ticketPage()
	{
		
		$by = Input::get('by');
		$type    = Input::get('type');
		$q  = Input::get('q');

		return $this->view('ticket', compact( 'by', 'type', 'q'));
	}
	
}
