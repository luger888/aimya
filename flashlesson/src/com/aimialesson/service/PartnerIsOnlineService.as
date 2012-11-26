package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class PartnerIsOnlineService extends AimiaService
	{
		
		public function PartnerIsOnlineService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().getIsOnlineUrl;
			params = new Object();
			params.user_id = User.getInstance().partnerID;
			if (User.getInstance().sessionID){
				params.PHPSESSID = User.getInstance().sessionID;
			}
			isPermanent = true;
		}
		
		override protected function onSuccess ( result : Object ) : void {
			var o:Object = new Object();
			if (result.data == "true") {
				User.getInstance().partnerIsOnline = true;
			} else {
				User.getInstance().partnerIsOnline = false;
			}
			//this.dispatchEvent( new ServiceEvent ( ServiceEvent.PARTNER_ISONLINE_RESULT, o));
		}		
	}
}