package com.aimialesson.events
{
	import flash.events.Event;
	
	public class MediaEvent extends Event
	{
		public static const CAM_PAUSE_TOGGLE:String = "camPauseToggle";
		public static const MIC_PAUSE_TOGGLE:String = "micPauseToggle";
		public static const MY_CAM_PAUSE_TOGGLE:String = "myCamPauseToggle";
		public static const MY_MIC_PAUSE_TOGGLE:String = "myMicPauseToggle";
		public static const PARTNER_CAM_PAUSE_TOGGLE:String = "partnerCamPauseToggle";
		public static const PARTNER_MIC_PAUSE_TOGGLE:String = "partnerMicPauseToggle";
		
		public function MediaEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new MediaEvent(type);
		}
	}
}