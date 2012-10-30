package com.aimialesson.UI.views
{
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.EventDispatcher;
	
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="MOVE_TO_LEFT", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="MOVE_TO_RIGHT", type="com.aimialesson.events.PresentationEvent")]
	public class MainUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var videoChat:VideoChatUI;
		[SkinPart (required="true")]
		public var textChat:TextChatUI;
		[SkinPart (required="true")]
		public var presentation:PresentationUI;
		[SkinPart (required="true")]
		public var debugger:TextArea;
		
		public function MainUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == debugger ) {
				Main.getInstance().debugger = debugger;
			} else if ( instance == presentation ) {
				(instance as EventDispatcher).addEventListener( PresentationEvent.MOVE_TO_LEFT, onPresentationEvent );
				(instance as EventDispatcher).addEventListener( PresentationEvent.MOVE_TO_RIGHT, onPresentationEvent );
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
		
		public function connectionInit () : void {
			debug("connectionInit");
			videoChat.myVideoInit();
			videoChat.partnerVideoInit();
			textChat.initChat();
		}
		
		private function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainUI:onPresentationEvent " + event.type);
			this.dispatchEvent ( event );
		} 
		
		private function debug ( mes : String) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += mes + "\n";
		}
	}
}