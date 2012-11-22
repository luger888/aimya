package com.aimialesson.model
{
	import com.aimialesson.model.Main;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;

	public class Presentation extends EventDispatcher
	{
		
		//private var fakeImages:Array = ["images/IMG_1120.JPG","images/IMG_1121.JPG","images/IMG_1122.JPG","images/IMG_1123.JPG","images/IMG_1124.JPG"];
		
		private var presentationImageUrls:ArrayCollection;// = new ArrayCollection(fakeImages);
		[Bindable]
		public var defaultImageURL:String;// = "images/IMG_1120.JPG";
		
		public function set imageUrls ( value : ArrayCollection ) : void {
			if (value) {
				presentationImageUrls = value;
				dispatchEvent( new Event ( "loadedChange" ) );
				dispatchEvent( new Event ( "currentImageURLChange" ) );
			}
		}
		
		[Bindable(Event="loadedChange")]
		public function set loaded ( value : Boolean) : void {
			
		}
		
		public function get loaded () : Boolean {
			return (presentationImageUrls && presentationImageUrls.length) 
		}
		
		public function get imagesNumber () : int {
			if (!presentationImageUrls){
				return 0;
			} else return presentationImageUrls.length;
		}
		[Bindable(Event="currentImageURLChange")]
		public function get currentImageURL () : String {
			if (!presentationImageUrls){
				return "";
			} else{
				debug("Presentation currentImageURL:" + Media.getInstance().domain + presentationImageUrls.getItemAt(currentImageN) as String);
				return Media.getInstance().domain + (presentationImageUrls.getItemAt(currentImageN) as String);
			} 
		}
		
		public function set currentImageURL (value:String) : void {
			//debug("imitate of");
		}

		private var currentImageN:int = 0;
		public function set currentImageNumber ( value : int ) : void {
			if (value >= imagesNumber){
				currentImageN = 0;
			} else if (value < 0) {
				currentImageN = imagesNumber - 1; 
			} else {
				currentImageN = value;
			}
			debug("Presentation currentImageN:" + currentImageN);
			dispatchEvent( new Event ( "currentImageURLChange" ) );
			//just to update currentImageURL in viewvs 
			
		}
		
		public function get currentImageNumber ( ) : int {
			return currentImageN;
		}
		
		
		public function Presentation()
		{
		}
		
		private static var instance:Presentation;
		public static function getInstance() : Presentation {
			if (instance == null){
				instance = new Presentation();
			}
			return instance;
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}