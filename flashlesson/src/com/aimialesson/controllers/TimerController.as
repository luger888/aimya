package com.aimialesson.controllers
{
	import com.aimialesson.events.AimyaTimerEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.User;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	
	[Event (name="timerEvent", type="com.aimialesson.events.AimyaTimerEvent")]
	public class TimerController extends EventDispatcher
	{
		
		public static const INTERVAL_IN_SEC:int = 1;
		private var timer:Timer = new Timer(INTERVAL_IN_SEC * 1000);
		
		public function TimerController()
		{
		}
		
		public function start() : void {
			User.getInstance().addEventListener(User.USER_IS_ONLINE_CHANGE, changeTimerStatus);
			User.getInstance().addEventListener(User.PARTNER_IS_ONLINE_CHANGE, changeTimerStatus);
			timer.addEventListener(TimerEvent.TIMER, onTimer);
			timer.start();
		}
		
		public function stop() : void {
			timer.stop();
		}
		
		private function changeTimerStatus( event : Event ) : void {
			debug("changeTimerStatus");
			debug (User.getInstance().partnerIsOnline.toString());
			if (User.getInstance().isOnline && User.getInstance().partnerIsOnline){
				timer.start();
			} else {
				timer.stop();
			}
		}
		
		private function onTimer( event : TimerEvent ) : void {
//			debug("onTimer");
			this.dispatchEvent( new AimyaTimerEvent ( AimyaTimerEvent.TIMER_EVENT ) ); 
		}
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}