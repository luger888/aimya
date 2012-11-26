package com.aimialesson.events
{
	import flash.events.Event;
	
	public class NotesEvent extends Event
	{
		public static const ADD_NEW_LINE:String = "addNewLine";
		
		public var value:Object;
		
		public function NotesEvent(type:String, _value:Object, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			value = _value;
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new NotesEvent(type, value);
		}
	}
}