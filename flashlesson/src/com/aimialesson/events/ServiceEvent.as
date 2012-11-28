package com.aimialesson.events
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;
	
	
	public class ServiceEvent extends Event
	{
		public static const GET_PRESENTATION_IMAGES_RESULT:String = "getPresentationImagesResult";
		public static const SESSION_IS_STARTED_RESULT:String = "sessionIsStartedResult";
		public static const SESSION_IS_STOPPED_RESULT:String = "sessionIsStoppedResult";
		public static const RESIZE_RESULT:String = "resizeResult";
		
		public var value:Object;
		
		public function ServiceEvent(type:String, value:Object = null, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			this.value = value;
		}
		
		override public function clone():Event {
			return new ServiceEvent(type, value, bubbles, cancelable);
		}
		
	}
}