package com.aimialesson.model
{
	import com.aimialesson.model.Main;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;

	[Bindable]
	public class User extends EventDispatcher
	{
		private static var instance:User;
		public static const TEACHER:String = "2";
		public static const STUDENT:String = "1";
		
		public function User() {
		}
		
		private var _userName:String = "";
		public function set userName ( value : String ) : void {
			_userName = value;
		}
		
		public function get userName () : String {
			return _userName;
		}
		
		private var _userRole:String = "";
		public function set userRole ( value : String ) : void {
			_userRole = value;
			this.dispatchEvent( new Event("partnerRoleChange") );
		}
		
		public function get userRole () : String {
			return (_userRole == User.STUDENT) ? "Student" : "Instructor";
		}
		
		private var _isOnline:Boolean = false;
		public function set isOnline ( value : Boolean ) : void {
			_isOnline = value;
		}
		
		public function get isOnline () : Boolean {
			return _isOnline;
		}
		
		private var _partnerName:String = "";
		public function set partnerName ( value : String ) : void {
			_partnerName = value;
		}
		
		public function get partnerName () : String {
			return _partnerName;
		}
		
		private var _partnerRole:String = "";
		[Bindable(Event="partnerRoleChange")]
		public function set partnerRole ( value : String ) : void {
			_partnerRole = value;
		}
		
		public function get partnerRole () : String {
			return (_userRole == User.TEACHER) ? "Student" : "Instructor";
		}
		
		private var _partnerIsOnline:Boolean = false;
		public function set partnerIsOnline ( value : Boolean ) : void {
			_partnerIsOnline = value;
		}
		
		public function get partnerIsOnline () : Boolean {
			return _partnerIsOnline;
		}
		
		private var _partnerID:String = "";
		public function set partnerID ( value : String ) : void {
			_partnerID = value;
		}
		
		public function get partnerID () : String {
			return _partnerID;
		}
		
		private var _userID:String = "";
		public function set userID ( value : String ) : void {
			_userID = value;
		}
		
		public function get userID () : String {
			return _userID;
		}
		
		private var _sessionID:String = "";
		public function set sessionID ( value : String ) : void {
			_sessionID = value;
		}
		
		public function get sessionID () : String {
			return _sessionID;
		}
		
		private var _lessonID:String = "";
		public function set lesson_id ( value : String ) : void {
			_lessonID = value;
		}
		
		public function get lesson_id () : String {
			return _lessonID;
		}
		
		public static function getInstance() : User {
			if (instance == null){
				instance = new User();
			}
			return instance;
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}