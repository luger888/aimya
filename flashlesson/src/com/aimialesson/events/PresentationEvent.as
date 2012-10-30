package com.aimialesson.events
{
	import flash.events.Event;
	
	public class PresentationEvent extends Event
	{
		public static const MOVE_TO_LEFT:String = "moveToLeft";
		public static const MOVE_TO_RIGHT:String = "moveToRight";
		
		public function PresentationEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new PresentationEvent(type);
		}
	}
}