package com.aimialesson.events
{
	import flash.events.Event;
	
	public class AimyaTimerEvent extends Event
	{
		public static const TIMER_EVENT:String = "timerEvent";
		//public static const MY_STREAM_INIT_COMPLETE:String = "myStreamInitComplete";
		//public static const PARTNER_STREAM_INIT_COMPLETE:String = "partnerStreamInitComplete";
		//public static const NET_CONNECTION_INIT_COMPLETE:String = "netConnectInitComplete";
		
		public function AimyaTimerEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new AimyaTimerEvent(type);
		}
	}
}