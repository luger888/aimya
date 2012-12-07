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
	
	import spark.components.Button;
	import spark.components.Group;
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	public class VideoChatUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var myAimiaVideo:AimiaVideoUI;
		[SkinPart (required="true")]
		public var partnerAimiaVideo:AimiaVideoUI;
		[SkinPart (required="true")]
		public var muteMicBtn:Button;
		[SkinPart (required="true")]
		public var muteCamBtn:Button;

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
			if ( instance == muteMicBtn || instance == muteCamBtn ) {
				(instance as Button).addEventListener(MouseEvent.CLICK, onBtnClick);
			}
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			if ( instance == muteMicBtn || instance == muteCamBtn ) {
				(instance as Button).removeEventListener(MouseEvent.CLICK, onBtnClick);
			}
		}
		
		private function onBtnClick ( event : MouseEvent ) : void {
			switch (event.target) {
				case muteMicBtn	:	muteMic();
					break;
				case muteCamBtn	:	muteCam();
					break;
			}
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
			Main.getInstance().addEventListener( Main.SESSION_STARTED_CHANGED, onSessionStartedChange );
		}
		
		public function onLessonFinished () : void {
			debug("VideoChat:onStopSession");
			myVideo.clear();
			myVideo.attachCamera(null);
			partnerVideo.clear();
			partnerVideo.attachNetStream(null);
		}
		
		public function muteMic() : void {
			debug("VideoChat:muteMic");
			Media.getInstance().micPaused = !Media.getInstance().micPaused;
			if (Media.getInstance().micPaused){
				Media.getInstance().myNetStream.attachAudio(null);
			} else {
				Media.getInstance().myNetStream.attachAudio(mic);
			}
		}
		
		public function muteCam() : void {
			debug("VideoChat:muteCam");
			Media.getInstance().camPaused = !Media.getInstance().camPaused;
			if (Media.getInstance().camPaused){
				Media.getInstance().myNetStream.attachCamera(null);
				myVideo.attachCamera(null);
				myVideo.visible = false;
				myVideo.clear();
			} else {
				myVideo.visible = true;
				myVideo.attachCamera(cam);
				Media.getInstance().myNetStream.attachCamera(cam);
			}
			
		}

		
		private function onSessionStartedChange (event:Event) : void {
			if (Main.getInstance().session_started) Media.getInstance().partnerNetStream.play(Media.getInstance().partnerStreamName);
			else Media.getInstance().partnerNetStream.pause();
		}
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}