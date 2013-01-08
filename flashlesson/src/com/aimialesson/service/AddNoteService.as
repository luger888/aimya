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
			params.date = formateDate(value.date);
		}
		
		private function formateDate ( dateNum : Number ) : String {
			var date:Date = new Date();
			date.setTime(dateNum);
			var d:String = date.date.toString();
			if (d.length == 1) d = "0" + d;
			var month:String = (date.month + 1).toString();
			if (month.length == 1) month = "0" + month;
			var hours:String = (date.hours - User.getInstance().timeZone).toString();
			if (hours.length == 1) hours = "0" + hours;
			var minutes:String = date.minutes.toString();
			if (minutes.length == 1) minutes = "0" + minutes;
			var fullYear:String = date.fullYear.toString();
			fullYear = fullYear.substr(2,2);
			var dateStr:String = month + "." + d + "." + fullYear + " " + hours + ":" + minutes;
			return dateStr; 
		}
		
		override protected function onSuccess ( result : Object ) : void {
			
		}		
	}
}