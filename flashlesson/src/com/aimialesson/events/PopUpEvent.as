package com.aimialesson.events
{
	import flash.events.Event;
	
	public class PopUpEvent extends Event
	{
		public static const YES_POP_UP_BTN:String = "yesPopUpBtn";
		public static const NO_POP_UP_BTN:String = "noPopUpBtn";
		public static const CLOSE:String = "closeBtn";
		
		public function PopUpEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new PopUpEvent(type);
		}
	}
}