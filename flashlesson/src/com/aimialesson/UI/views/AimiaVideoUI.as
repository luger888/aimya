package com.aimialesson.UI.views
{
	import com.aimialesson.UI.views.elements.Lamp;
	
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.media.Video;
	
	import mx.core.UIComponent;
	
	import spark.components.Button;
	import spark.components.RichText;
	import spark.components.supportClasses.SkinnableComponent;
	
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
		
		
		public const VIDEO_CHAT_WINDOW_HEIGHT:int = 105;
		public const VIDEO_CHAT_WINDOW_WIDTH:int = 140;

		private var _video:Video;
		
		public function AimiaVideoUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == videoContainter ) {
				_video = new Video(VIDEO_CHAT_WINDOW_WIDTH, VIDEO_CHAT_WINDOW_HEIGHT);
				_video.scaleX = -1;
				_video.x = _video.width;
				videoContainter.addChild(_video);				
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
		
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
			
		public function get video () : Video {
			return _video;
		}
		
	}
}