package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	[Event (name="getCurrentTimeResult", type="com.aimialesson.events.ServiceEvent")]
	public class UserIsOnlineService extends AimiaService
	{
		
		public function UserIsOnlineService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().getIsOnlineUrl;
			params.user_id = User.getInstance().userID;
			isPermanent = true;
		}
		
		override protected function onSuccess ( result : Object ) : void {
			var o:Object = result;
			/*if (result.data == "true") {
				User.getInstance().isOnline = true;
			} else {
				User.getInstance().isOnline = false;
			}*/
			makeCall();
			this.dispatchEvent( new ServiceEvent ( ServiceEvent.GET_CURRENT_TIME_RESULT, o));
		}		
	}
}