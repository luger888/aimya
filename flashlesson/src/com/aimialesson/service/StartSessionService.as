package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class StartSessionService extends AimiaService
	{
		
		public function StartSessionService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().startSessionUrl;
			params.lesson_id = User.getInstance().lesson_id;
		}
		
		public function addParams ( value : Object) : void {
		}
		
		override protected function onSuccess ( result : Object ) : void { 
			this.dispatchEvent( new ServiceEvent ( ServiceEvent.SESSION_IS_STARTED_RESULT));
		}
	}
}