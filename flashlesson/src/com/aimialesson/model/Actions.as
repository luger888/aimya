package com.aimialesson.model
{
	public class Actions
	{
		
		private static var instance:Actions;
		
		public var domain:String = "";
		
		private const FILE_UPLOAD_URL:String = "/lesson/upload/";//"/test/upload.php";
		private const PRESENTATION_IMAGES_URL:String = "/lesson/files/";
		private const GET_IS_ONLINE_URL:String;
		private const ADD_NOTE_URL:String;

		
		public function Actions()
		{
		}
		
		public static function getInstance():Actions {
			if (instance == null){
				instance = new Actions();
			}
			return instance;
		}
		
		public function get fileUploadUrl () : String {
			return this.domain + FILE_UPLOAD_URL;
		}
		
		public function get getPresentaionImagesUrl () : String {
			return this.domain + PRESENTATION_IMAGES_URL;
		}

		public function get getIsOnlineUrl () : String {
			return this.domain + GET_IS_ONLINE_URL;
		}
		
		public function get addNoteUrl () : String {
			return this.domain + ADD_NOTE_URL;
		}
	}
}