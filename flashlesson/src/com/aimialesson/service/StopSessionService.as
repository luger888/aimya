package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class StopSessionService extends AimiaService
	{
		
		public function StopSessionService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().stopSessionUrl;
			//			params.lesson_id = User.getInstance().lesson_id;
		}
		
		public function addParams ( value : Object) : void {
		}
		
		override protected function onSuccess ( result : Object ) : void {
			this.dispatchEvent( new ServiceEvent ( ServiceEvent.SESSION_IS_STOPPED_RESULT));
		}		
	}
}