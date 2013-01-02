package com.aimialesson.UI.views
{
	import com.aimialesson.events.MediaEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	
	import flash.events.*;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	
	import mx.controls.VideoDisplay;
	import mx.core.UIComponent;
	
	import spark.components.Button;
	import spark.components.Group;
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="myCamPauseToggle", type="com.aimialesson.events.MediaEvent")]
	[Event (name="myMicPauseToggle", type="com.aimialesson.events.MediaEvent")]
	[Event (name="partnerCamPauseToggle", type="com.aimialesson.events.MediaEvent")]
	[Event (name="partnerMicPauseToggle", type="com.aimialesson.events.MediaEvent")]
	public class VideoChatUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var myAimiaVideo:AimiaVideoUI;
		[SkinPart (required="true")]
		public var partnerAimiaVideo:AimiaVideoUI;

		private var myNS:NetStream;
		private var partnerNS:NetStream;
		public function VideoChatUI()
		{
			super();
		}

		override protected function partAdded(partName:String, instance:Object):void
		{
			if ( instance == myAimiaVideo || instance == partnerAimiaVideo ){
				(instance as AimiaVideoUI).addEventListener(MediaEvent.CAM_PAUSE_TOGGLE, onCamPause);
				(instance as AimiaVideoUI).addEventListener(MediaEvent.MIC_PAUSE_TOGGLE, onMicPause);
			}
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
			debug("VideoChat:myVideoInit");
			if ( Media.getInstance().cam != null ) 
			{
				debug("myVideo.attachCamera");
				myVideo.attachCamera(Media.getInstance().cam);
			}
		}
		
		public function partnerVideoInit():void {
			debug("VideoChat:partnerVideoInit");
			partnerVideo.attachNetStream(Media.getInstance().partnerNetStream);
		}
		
		public function onLessonFinished () : void {
			debug("VideoChat:onLessonFinished");
			myVideo.clear();
			myVideo.attachCamera(null);
			partnerVideo.clear();
			partnerVideo.attachNetStream(null);
		}
		
		private function onCamPause ( event : MediaEvent ) : void {
			switch (event.target) {
				case myAimiaVideo		:	this.dispatchEvent( new MediaEvent ( MediaEvent.MY_CAM_PAUSE_TOGGLE ) );
											muteMyVideo();
											break;
				case partnerAimiaVideo	:	this.dispatchEvent( new MediaEvent ( MediaEvent.PARTNER_CAM_PAUSE_TOGGLE ) );
											mutePartnerVideo();
											break;
			}
		}
		
		private function onMicPause ( event : MediaEvent ) : void {
			switch (event.target) {
				case myAimiaVideo		:	this.dispatchEvent( new MediaEvent ( MediaEvent.MY_MIC_PAUSE_TOGGLE ) );
											break;
				case partnerAimiaVideo	:	this.dispatchEvent( new MediaEvent ( MediaEvent.PARTNER_MIC_PAUSE_TOGGLE ) );
											break;
			}
		}
		
		public function muteMyVideo() : void {
			debug("VideoChat:muteMyCam");
			if (Media.getInstance().camPaused){
				myVideo.attachCamera(null);
				myVideo.clear();
			} else {
				myVideo.attachCamera(Media.getInstance().cam);
			}
		}

		public function mutePartnerVideo() : void {
			debug("VideoChat:muteMyCam");
			if (Media.getInstance().partnerCamPaused ){
				partnerVideo.clear();
				partnerVideo.attachNetStream(null);
			} else {
				partnerVideo.attachNetStream(Media.getInstance().partnerNetStream);
			}
		}

		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}