package com.aimialesson.model
{
	import com.aimialesson.model.Main;
	
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;
	import mx.events.CollectionEvent;

	public class Notes extends EventDispatcher
	{
		
		public static const NEW_LINE_ADDED:String = "newLineAdded";
				
		public function Notes()
		{
		}
		
		private var _notesAC:ArrayCollection;// = new ArrayCollection();//[{message:"test message 1", date:"1000", name:"user1"}, {message:"test message 2", date:"2000", name:"user2"}]);
		[Bindable]
		public function set notesAC ( value : ArrayCollection ) : void {
			_notesAC = value;
//			_notesAC.addEventListener(CollectionEvent.COLLECTION_CHANGE, onCollectionChange);
			//onCollectionChange(null);
			//notesAC.enableAutoUpdate();
			this.dispatchEvent(new Event ( Notes.NEW_LINE_ADDED ) );
		}
		
		public function get notesAC () : ArrayCollection {
			return _notesAC;
		}

		
		private var _newLineData:Object;
		public function set newLineData ( value : Object ) : void {
			if (value != _newLineData){
				_newLineData = value;
				if (!notesAC){
					notesAC = new ArrayCollection();
				} 
				_newLineData.isEven = isEven(); 
				notesAC.addItem(_newLineData);
				this.dispatchEvent(new Event ( Notes.NEW_LINE_ADDED ) );
			}
		}
		
	/*	public function onCollectionChange ( event : Event ) : void {
			this.dispatchEvent(new Event ( Notes.NEW_LINE_ADDED ) );
		}*/
		
		public function get newLineData () : Object {
			return _newLineData;
		}
		
		private var _text:String = "";
		[Bindable]
		public function set text ( value : String ) : void {
			_text = value;
		}
		
		public function get text () : String {
			return _text;
		}
		
		private var _newLine:String;
		public function set newLine ( value : String ) : void {
			if (value != _newLine){
				_newLine = value;
				text += value + "\n";
			}
		}
		
		public function get newLine () : String {
			return _newLine;
		}
		
		private static var instance:Notes;
		public static function getInstance() : Notes {
			if (instance == null){
				instance = new Notes();
			}
			return instance;
		}
		
		public function clear () : void {
			notesAC = null;
		}
		
		
		private function isEven() : Boolean {
			debug (String(Math.abs(notesAC.length / 2 - Math.ceil(notesAC.length / 2))));
			return (Math.abs(notesAC.length / 2 - Math.ceil(notesAC.length / 2)) == 1 / 2);
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}