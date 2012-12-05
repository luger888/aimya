package com.aimialesson.controllers
{
	import com.aimialesson.model.Main;
	import com.aimialesson.model.User;
	
	import flash.events.EventDispatcher;
	
	import spark.components.Application;

	public class UserController extends EventDispatcher
	{
		public function UserController()
		{
		}
		
		public function setParameters ( parameters : Object ) : void {
			if (parameters.userName){
				User.getInstance().userName = parameters.userName;
				debug ("userName:" + parameters.userName);
			}
			if (parameters.userRole){
				User.getInstance().userRole = parameters.userRole;
				debug ("userRole:" + parameters.userRole);
			}
			if (parameters.partnerName){
				User.getInstance().partnerName = parameters.partnerName;
				debug ("partnerName:" + parameters.partnerName);
			}
			if (parameters.PHPSESSID){
				User.getInstance().sessionID = parameters.PHPSESSID;
				debug ("sessionID(PHPSESSID):" + parameters.PHPSESSID);
			}
			if (parameters.lesson_id){
				User.getInstance().lesson_id = parameters.lesson_id;
				debug ("lessonID:" + parameters.lesson_id);
			}
			if (parameters.userID){
				User.getInstance().userID = parameters.userID;
				debug ("userID:" + parameters.userID);
			}
			if (parameters.partnerID){
				User.getInstance().partnerID = parameters.partnerID;
				debug ("partnerID:" + parameters.partnerID);
			}
		}
		

		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}