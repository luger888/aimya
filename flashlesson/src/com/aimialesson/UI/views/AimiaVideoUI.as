package com.aimialesson.UI.views
{
	import com.aimialesson.UI.views.elements.Lamp;
	import com.aimialesson.events.MediaEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.media.Video;
	
	import mx.core.UIComponent;
	
	import spark.components.Button;
	import spark.components.RichText;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="camPauseToggle", type="com.aimialesson.events.MediaEvent")]
	[Event (name="micPauseToggle", type="com.aimialesson.events.MediaEvent")]
	public class AimiaVideoUI extends SkinnableComponent
	{
		
		[SkinPart (required="true")]
		public var videoContainter:UIComponent;
		[SkinPart (required="true")]
		public var userNameField:RichText;
		[SkinPart (required="true")]
		public var userRoleField:RichText;
		[SkinPart (required="true")]
		public var lamp:Lamp;
		[SkinPart (required="true")]
		public var muteMicBtn:Button;
		[SkinPart (required="true")]
		public var muteCamBtn:Button;
		[SkinPart (required="true")]
		public var unMuteMicBtn:Button;
		[SkinPart (required="true")]
		public var unMuteCamBtn:Button;

		
		public const VIDEO_CHAT_WINDOW_HEIGHT_MIN_MODE:int = 105;
		public const VIDEO_CHAT_WINDOW_WIDTH_MIN_MODE:int = 140;
		public const VIDEO_CHAT_WINDOW_HEIGHT_MAX_MODE:int = 120;//150;//160;
		public const VIDEO_CHAT_WINDOW_WIDTH_MAX_MODE:int = 160;//200;//214;

		private var _video:Video;
		
		public function AimiaVideoUI()
		{
			super();
			Main.getInstance().addEventListener(Main.FS_MODE_CHANGED, onFSModeChanged);
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == videoContainter ) {
				_video = new Video(videoWidth, videoHeight);
				_video.scaleX = -1;
				_video.x = _video.width;
				videoContainter.addChild(_video);
			} else 	if ( instance == muteMicBtn || instance == muteCamBtn || instance == unMuteMicBtn || instance == unMuteCamBtn ) {
				(instance as Button).addEventListener(MouseEvent.CLICK, onBtnClick);
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			if ( instance == muteMicBtn || instance == muteCamBtn || instance == unMuteMicBtn || instance == unMuteCamBtn ) {
				(instance as Button).removeEventListener(MouseEvent.CLICK, onBtnClick);
			}
		}
		
		private var _userName:String;
		[Bindable(Event="userNameChange")]
		public function set userName ( value : String ) : void {
			_userName = value;
			dispatchEvent( new Event("userNameChange") );
		}
		
		public function get userName ( ) : String {
			return _userName;
		}
		
		private var _userRole:String;
		[Bindable(Event="userRoleChange")]
		public function set userRole ( value : String ) : void {
			_userRole = value;
			dispatchEvent( new Event("userRoleChange") );
		}
		
		public function get userRole ( ) : String {
			return _userRole;
		}
		
		private var _userOnline:Boolean;
		[Bindable(Event="userOnlineChange")]
		public function set userOnline ( value : Boolean ) : void {
			_userOnline = value;
			dispatchEvent( new Event("userOnlineChange") );
		}
		
		public function get userOnline ( ) : Boolean {
			return _userOnline;
		}
		
		private var _muteCam:Boolean;
		[Bindable(Event="muteCamChange")]
		public function set muteCam ( value : Boolean ) : void {
			_muteCam = value;
			dispatchEvent( new Event("muteCamChange") );
		}
		
		public function get muteCam ( ) : Boolean {
			return _muteCam;
		}
		
		private var _muteMic:Boolean;
		[Bindable(Event="muteMicChange")]
		public function set muteMic ( value : Boolean ) : void {
			_muteMic = value;
			dispatchEvent( new Event("muteMicChange") );
		}
		
		public function get muteMic ( ) : Boolean {
			return _muteMic;
		}
			
		public function get video () : Video {
			return _video;
		}
		
		[Bindable(Event="videoWidthChange")]
		public function set videoWidth ( value : int ) : void {
		}
		
		public function get videoWidth ( ) : int {
			var w:int;
			if (Main.getInstance().fsMode){
				w = VIDEO_CHAT_WINDOW_WIDTH_MAX_MODE;
			} else {
				w = VIDEO_CHAT_WINDOW_WIDTH_MIN_MODE;
			}
			return w;
		}
		
		[Bindable(Event="videoHeightChange")]
		public function set videoHeight ( value : int ) : void {
		}
		
		public function get videoHeight ( ) : int {
			var h:int;
			if (Main.getInstance().fsMode){
				h = VIDEO_CHAT_WINDOW_HEIGHT_MAX_MODE;
			} else {
				h = VIDEO_CHAT_WINDOW_HEIGHT_MIN_MODE;
			}
			return h;
		}
		
		[Bindable(Event="videoScaleXChange")]
		public function set videoScaleX ( value : Number ) : void {
		}
		
		public function get videoScaleX ( ) : Number {
			var sX:Number;
			if (Main.getInstance().fsMode){
				sX = VIDEO_CHAT_WINDOW_WIDTH_MAX_MODE / VIDEO_CHAT_WINDOW_WIDTH_MIN_MODE;
			} else {
				sX = 1;
			}
			return sX;
		}
		
		private function onBtnClick ( event : MouseEvent ) : void {
			switch (event.target) {
				case muteMicBtn		:	
				case unMuteMicBtn	:	this.dispatchEvent( new MediaEvent ( MediaEvent.MIC_PAUSE_TOGGLE ) );
										break;
				case unMuteCamBtn	:
				case muteCamBtn		:	this.dispatchEvent( new MediaEvent ( MediaEvent.CAM_PAUSE_TOGGLE ) );
										break;
			}
		}
		
		private function onFSModeChanged ( event : Event ) : void {

			_video.height = videoHeight;
			_video.width = videoWidth;
			_video.scaleX = -videoScaleX;
			_video.x = _video.width;
			
			dispatchEvent( new Event ("videoHeightChange"));
			dispatchEvent( new Event ("videoWidthChange"));
			dispatchEvent( new Event ("videoScaleXChange"));
		}
	}
}