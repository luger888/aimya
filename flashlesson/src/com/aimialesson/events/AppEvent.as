package com.aimialesson.events
{
	import flash.events.Event;
	
	public class AppEvent extends Event
	{
		public static const CONNECT_INIT_COMPLETE:String = "connectInitComplete";
		public static const LOAD_TEXTS_COMPLETE:String = "loadTextsComplete";
		public static const INIT_COMPLETE:String = "initComplete";
		public static const CHANGE_SCREEN_STATE:String = "changeScreenState";
		public static const START_SESSION:String = "startSession";
		public static const STOP_SESSION:String = "stopSession";
		//public static const MY_STREAM_INIT_COMPLETE:String = "myStreamInitComplete";
		//public static const PARTNER_STREAM_INIT_COMPLETE:String = "partnerStreamInitComplete";
		//public static const NET_CONNECTION_INIT_COMPLETE:String = "netConnectInitComplete";
		
		public function AppEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new AppEvent(type);
		}
	}
}