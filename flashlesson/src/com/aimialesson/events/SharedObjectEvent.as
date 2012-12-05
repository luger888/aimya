package com.aimialesson.events
{
	import flash.events.Event;
	
	public class SharedObjectEvent extends Event
	{
		public static const SHARED_PRESENTATION_UPLOADED:String = "sharedPresentationUploaded";
		public static const LESSON_IS_FINISHED:String = "lessonIsFinished";
		
		public function SharedObjectEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new SharedObjectEvent(type);
		}
	}
}