package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class UserIsOnlineService extends AimiaService
	{
		
		public function UserIsOnlineService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().getIsOnlineUrl;
			params = new Object();
			params.user_id = User.getInstance().userID;
			if (User.getInstance().sessionID){
				params.PHPSESSID = User.getInstance().sessionID;
			}
			isPermanent = true;
		}
		
		override protected function onSuccess ( result : Object ) : void {
			var o:Object = new Object();
			if (result.data == "true") {
				User.getInstance().isOnline = true;
			} else {
				User.getInstance().isOnline = false;
			}
			makeCall();
			//this.dispatchEvent( new ServiceEvent ( ServiceEvent.USER_ISONLINE__RESULT, o));
		}		
	}
}