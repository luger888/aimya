package com.aimialesson.events
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;
	
	
	public class ServiceEvent extends Event
	{
		public static const GET_PRESENTATION_IMAGES_RESULT:String = "getPresentationImagesResult";
		
		public var value:Object;
		
		public function ServiceEvent(type:String, value:Object, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			this.value = value;
		}
		
		override public function clone():Event {
			return new ServiceEvent(type, value, bubbles, cancelable);
		}
		
	}
}