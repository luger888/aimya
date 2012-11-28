package com.aimialesson.UI.views
{
	import com.aimialesson.UI.skins.NotesSkin;
	import com.aimialesson.events.NotesEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.User;
	
	import flash.events.*;
	
	import spark.components.Button;
	import spark.components.List;
	import spark.components.TextArea;
	import spark.components.TextInput;
	import spark.components.supportClasses.SkinnableComponent;

	[Event (name="addNewLine", type="com.aimialesson.events.NotesEvent")]
	public class NotesUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var notesList:List;
		[SkinPart (required="true")]
		public var messageInput:TextArea;
		[SkinPart (required="true")]
		public var sendBtn:Button;
		
		private static var instance:NotesUI;
		public function NotesUI()
		{
			super();
			//setStyle("skinClass", NotesSkin);
		}
		
		public static function getInstance():NotesUI{
			trace("getInstance:" + instance);
			if (instance == null){
				instance = new NotesUI();
			}
			return instance;
		}
		
		override protected function partAdded(partName:String, instance:Object):void
		{
			trace("NotesUI:partAdded:" + instance);
			if (instance == notesList) {
	//			user = User.getInstance();
				//initChat();
			} else {
				if (instance == sendBtn){
					sendBtn.addEventListener(MouseEvent.CLICK, onClickSendBtn);
				}
				if (instance == messageInput) {
					messageInput.addEventListener(KeyboardEvent.KEY_DOWN, onKeyDown);
				}
			}
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			
		}
		
		private function onClickSendBtn(event:MouseEvent):void
		{
	//		messageInput.text = Media.getInstance().userName + ": " + messageInput.text;
			var date:Date = new Date();
			debug ("date.getTime():" + date.getTime());
			this.dispatchEvent( new NotesEvent ( NotesEvent.ADD_NEW_LINE,  {message:messageInput.text, name:User.getInstance().userName, date:date.getTime()}) );
			messageInput.text = "";
		}
		
		private function onKeyDown(ev:KeyboardEvent):void {
			if (ev.keyCode==13){
				onClickSendBtn(null);
			}
		}
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}
class SingletonEnforcer{}