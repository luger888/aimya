package com.aimialesson.UI.views
{
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Main;
	
	import flash.events.*;
	import flash.net.*;
	
	import spark.components.Button;
	import spark.components.TextArea;
	import spark.components.TextInput;
	import spark.components.supportClasses.SkinnableComponent;

	public class TextChatUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var chatArea:TextArea;
		[SkinPart (required="true")]
		public var messageInput:TextInput;
		[SkinPart (required="true")]
		public var sendBtn:Button;
		private var so:SharedObject; 
		
		public function TextChatUI()
		{
			super();
		}
		override protected function partAdded(partName:String, instance:Object):void
		{
			if (instance == chatArea){
				//initChat();
			} else {
				if (instance == sendBtn){
					sendBtn.addEventListener(MouseEvent.CLICK, onClickSendBtn);
				}
			}
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			
		}
		
		public function initChat():void
		{
			so = SharedObject.getRemote(Media.getInstance().chatID, Media.getInstance().nc.uri, false);
//			so = SharedObject.getRemote("SampleChat", "rtmp://localhost/SOSample", false);
			so.addEventListener(SyncEvent.SYNC, soOnSync);
			so.client    = this;
			so.connect(Media.getInstance().nc);
		}
		
		private function onClickSendBtn(event:MouseEvent):void
		{
			so.setProperty("chatMessage", messageInput.text);
		}
				
		private function onReply(result:Object):void 
		{
			if (!result)    return;
			
			debug("onReply : resultObj = " + result);
			for ( var prop:String in result)
			{
				debug("prop : "+prop+" = "+result[prop]);
			}
		}
		
		private function soOnSync(event:SyncEvent):void
		{
			debug("soOnSync");
			
			for (var prop:String in so.data) 
			{
				debug("prop "+prop+" = "+so.data[prop]);
			}
			if (so.data['chatMessage'] != undefined)
				chatArea.text += so.data['chatMessage'] + "\n";
		}
		
		
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}