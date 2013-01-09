package com.aimialesson.model
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import spark.components.TextArea;

	public class Time extends EventDispatcher
	{
		
		public static var SHOW_FIRST_WARNING:String = "showFirstWarning";
		public static var SHOW_SECOND_WARNING:String = "showSecondWarning";
		public static var REMAINING_TIME_CHANGED:String = "remainingTimeChanged";
		
		// in minutes
		public static var FIRST_WARNING_VALUE:int = 5;
		public static var SECOND_WARNING_VALUE:int = 1;
		
		private var firstWarningIsShown:Boolean = false;
		private var secondWarningIsShown:Boolean = false;
		
		private var _remainingTime:int = 0;
		[Bindable(Event=Time.REMAINING_TIME_CHANGED)]
		public function set remainingTime ( value : int ) : void {
			if (_remainingTime == 0 && value >= 0 ){
				initWarnings(value);
			}
			_remainingTime = value;
			trace("_remainingTime:" + _remainingTime);
			dispatchEvent( new Event (Time.REMAINING_TIME_CHANGED) );
			checkForWarning();
		}  
		
		public function get remainingTime ( ) : int {
			return _remainingTime; 
		}
		
		private function initWarnings ( value : int ) : void {
			if (value <  FIRST_WARNING_VALUE  * 60){
				firstWarningIsShown = true;
			}
			if (value <  SECOND_WARNING_VALUE * 60){
				secondWarningIsShown = true;
			}
		}
		
		private function checkForWarning ( ) : void {
			if (_remainingTime <= FIRST_WARNING_VALUE * 60 && !firstWarningIsShown){
				firstWarningIsShown = true;
				dispatchEvent( new Event (Time.SHOW_FIRST_WARNING) );
			}
			if (_remainingTime <= SECOND_WARNING_VALUE * 60 && !secondWarningIsShown){
				secondWarningIsShown = true;
				dispatchEvent( new Event (Time.SHOW_SECOND_WARNING) );
			}
		}
		
		[Bindable]
		public var totalTime:int = 360;
		
		
		private static var instance : Time;
		
		public function Time () {
		}
		
		public static function getInstance () : Time {
			if (instance == null){
				instance = new Time();
			}
			return instance;
		}
	}
}