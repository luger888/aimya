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
			/*if (parameters.teacherId){
				User.getInstance().teacherID = parameters.teacherId;
				debug ("teacherId:" + parameters.teacherId);
			}
			if (parameters.studentId){
				User.getInstance().studentID = parameters.studentId;
				debug ("studentId:" + parameters.studentId);
			}*/
			if (parameters.partnerId){
				User.getInstance().partnerID = parameters.partnerId;
				debug ("partnerId:" + parameters.partnerId);
			}
			if (parameters.userId){
				User.getInstance().userID = parameters.userId;
				debug ("userId:" + parameters.userId);
			}
		}
		

		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}