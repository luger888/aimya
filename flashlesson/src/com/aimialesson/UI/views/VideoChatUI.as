package com.aimialesson.UI.views
{
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	
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
		public var myAimiaVideo:AimiaVideoUI;
		[SkinPart (required="true")]
		public var partnerAimiaVideo:AimiaVideoUI;
		private var myNS:NetStream;
		private var partnerNS:NetStream;
		private var cam:Camera;
		private var mic:Microphone;
		public function VideoChatUI()
		{
			super();
		}

		override protected function partAdded(partName:String, instance:Object):void
		{
			/*if (instance == myVideoContainter){
				myVideo = new Video(VIDEO_CHAT_WINDOW_WIDTH, VIDEO_CHAT_WINDOW_HEIGHT);
				myVideoContainter.addChild(myVideo);
			} else 	if (instance == partnerVideoContainter)
			{
				partnerVideo = new Video(VIDEO_CHAT_WINDOW_WIDTH, VIDEO_CHAT_WINDOW_HEIGHT);
				partnerVideoContainter.addChild(partnerVideo);
			} */
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			
		}
		
		private function get partnerVideo():Video {
			return partnerAimiaVideo.video;
		}
		
		private function get myVideo():Video {
			return myAimiaVideo.video;
		}
		
		public function myVideoInit():void {
			cam = Camera.getCamera();
			mic = Microphone.getMicrophone();
			if ( cam != null ) 
			{
				cam.setMode(320, 240, 32);
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
			Media.getInstance().partnerNetStream.play(Media.getInstance().partnerStreamName);
			partnerVideo.attachNetStream(Media.getInstance().partnerNetStream);
		}
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}