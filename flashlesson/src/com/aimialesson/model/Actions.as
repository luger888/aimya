package com.aimialesson.model
{
	public class Actions
	{
		
		private static var instance:Actions;
		
		public var domain:String = "http://aimya.svitla.com";
		//public var domain:String = "";
		public var domain_add:String = "/en";
		
		private const FILE_UPLOAD_URL:String = "/lesson/upload/";//"/test/upload.php";
		private const PRESENTATION_IMAGES_URL:String = "/lesson/files/";
		private const GET_IS_ONLINE_URL:String = "/account/online/";
		private const ADD_NOTE_URL:String = "/lesson/notes/";
		private const START_SESSION_URL:String = "/lesson/start/";
		private const STOP_SESSION_URL:String = "/lesson/end/";
		private const RESIZE_URL:String = "/lesson/join/";

		
		public function Actions()
		{
		}
		
		public static function getInstance():Actions {
			if (instance == null){
				instance = new Actions();
			}
			return instance;
		}
		
		public function get urlbase ( ) : String {
			return this.domain + this.domain_add;
		}
		
		public function get fileUploadUrl () : String {
			return this.urlbase + FILE_UPLOAD_URL;
		}
		
		public function get getPresentaionImagesUrl () : String {
			return this.urlbase + PRESENTATION_IMAGES_URL;
		}

		public function get getIsOnlineUrl () : String {
			return this.urlbase + GET_IS_ONLINE_URL;
		}
		
		public function get addNoteUrl () : String {
			return this.urlbase + ADD_NOTE_URL;
		}
		
		public function get startSessionUrl () : String {
			return this.urlbase + START_SESSION_URL;
		}
		
		public function get stopSessionUrl () : String {
			return this.urlbase + STOP_SESSION_URL;
		}
		
		public function get resizeUrl () : String {
			return this.urlbase + RESIZE_URL;
		}
	}
}