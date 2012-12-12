package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.User;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class AddNoteService extends AimiaService
	{
		
		public function AddNoteService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().addNoteUrl;
			params.lesson_id = User.getInstance().lesson_id;
			params.user_id = User.getInstance().userID;
		}
		
		public function addParams ( value : Object) : void {
			params.message = value.message;
			params.name = value.name;
			params.date = value.date;
		}
		
		override protected function onSuccess ( result : Object ) : void {
			
		}		
	}
}