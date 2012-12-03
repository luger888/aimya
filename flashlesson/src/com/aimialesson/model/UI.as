package com.aimialesson.model
{
	import com.aimialesson.UI.views.NotesUI;

	public class UI
	{
		
		private static var instance:Actions;
		
		public var notes:NotesUI;
		
		public function UI()
		{
		}
		
		public static function getInstance():Actions {
			if (instance == null){
				instance = new Actions();
			}
			return instance;
		}
		
	}
}