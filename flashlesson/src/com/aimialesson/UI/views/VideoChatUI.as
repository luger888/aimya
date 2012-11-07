package com.aimialesson.UI.views
{
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Main;
	
	import flash.events.*;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	
	import mx.controls.VideoDisplay;
	import mx.core.UIComponent;
	
	import spark.components.Group;
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	public class VideoChatUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var myVideoContainter:UIComponent;
		[SkinPart (required="true")]
		public var partnerVideoContainter:UIComponent;
		public var debugOut:TextArea;
		private var myNS:NetStream;
		private var partnerNS:NetStream;
		private var cam:Camera;
		private var mic:Microphone;
		private var partnerVideo:Video;
		private var myVideo:Video;
		private const VIDEO_CHAT_WINDOW_HEIGHT:int = 150;
		private const VIDEO_CHAT_WINDOW_WIDTH:int = 200;
		public function VideoChatUI()
		{
			super();
		}

		override protected function partAdded(partName:String, instance:Object):void
		{
			if (instance == myVideoContainter){
				myVideo = new Video(VIDEO_CHAT_WINDOW_WIDTH, VIDEO_CHAT_WINDOW_HEIGHT);
				myVideoContainter.addChild(myVideo);
			} else 	if (instance == partnerVideoContainter)
			{
				partnerVideo = new Video(VIDEO_CHAT_WINDOW_WIDTH, VIDEO_CHAT_WINDOW_HEIGHT);
				partnerVideoContainter.addChild(partnerVideo);
			} 
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			
		}
		
		public function myVideoInit():void {
			//return;
			cam = Camera.getCamera();
			mic = Microphone.getMicrophone();
			if ( cam != null ) 
			{
				cam.setMode(640, 480, 24);
				cam.setQuality(0, 80);
				Media.getInstance().myNetStream.attachCamera(cam);
				myVideo.attachCamera(cam);
			}
			if ( mic != null) 
			{
				Media.getInstance().myNetStream.attachAudio(mic);
			}
			//Media.getInstance().myNetStream.bufferTime = 3;
			Media.getInstance().myNetStream.publish(Media.getInstance().myStreamName, "record");
		}
		
		public function partnerVideoInit():void {
			debug("VideoChat:partnerVideoInit");
			//return;
			Media.getInstance().partnerNetStream.play(Media.getInstance().partnerStreamName);
			partnerVideo.attachNetStream(Media.getInstance().partnerNetStream);
		}
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}