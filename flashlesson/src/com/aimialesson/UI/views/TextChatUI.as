package com.aimialesson.UI.views
{
	import com.aimialesson.events.TextChatEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	//import com.aimialesson.model.User;
	
	import flash.events.*;
	
	import spark.components.Button;
	import spark.components.TextArea;
	import spark.components.TextInput;
	import spark.components.supportClasses.SkinnableComponent;

	[Event (name="addNewLine", type="com.aimialesson.events.TextChatEvent")]
	public class TextChatUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var chatArea:TextArea;
		[SkinPart (required="true")]
		public var messageInput:TextInput;
		[SkinPart (required="true")]
		public var sendBtn:Button;
		
//		private var user:User;
		
		public function TextChatUI()
		{
			super();
		}
		override protected function partAdded(partName:String, instance:Object):void
		{
			if (instance == chatArea) {
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
			messageInput.text = Media.getInstance().userName + ": " + messageInput.text; 
			this.dispatchEvent( new TextChatEvent ( TextChatEvent.ADD_NEW_LINE,  messageInput.text) );
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