package com.aimialesson.UI.views
{
	import com.aimialesson.events.NotesEvent;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.EventDispatcher;
	
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="moveToLeft", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="moveToRight", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="presentationUploaded", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="addNewLine", type="com.aimialesson.events.NotesEvent")]
	public class MainUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var videoChat:VideoChatUI;
		[SkinPart (required="true")]
		public var textChat:NotesUI;
		[SkinPart (required="true")]
		public var presentation:PresentationUI;
		[SkinPart (required="true")]
		public var upload:UploadUI;
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
				
			} else if ( instance == textChat ) {
				(instance as EventDispatcher).addEventListener( NotesEvent.ADD_NEW_LINE, onTextChatEvent );
			} else if ( instance == upload) {
				(instance as EventDispatcher).addEventListener( PresentationEvent.PRESENTATION_UPLOADED, onPresentationEvent );
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
		
		public function connectionInit () : void {
			debug("connectionInit");
			videoChat.myVideoInit();
			videoChat.partnerVideoInit();
		}
		
		private function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainUI:onPresentationEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function onTextChatEvent ( event : NotesEvent ) : void {
			debug("MainUI:onTextChatEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function debug ( mes : String) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += mes + "\n";
		}
	}
}