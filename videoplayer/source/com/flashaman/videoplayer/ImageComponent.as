//////////////////////////////////
// VIDEO PLAYER v3--------------//
// IMAGE COMPONENT -------------//
// SUPPORT : -------------------//
// contact@flashaman.com -------//
// bpaul3262@yahoo.com ---------//
//////////////////////////////////

package com.flashaman.videoplayer
{
	//REGION IMPORTS
	
	// -- IMPORT DISPLAY
	import flash.display.MovieClip;
	import flash.display.Loader;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.LoaderInfo;
	
	// -- IMPORT EVENTS
	import flash.events.Event;
	import flash.events.ProgressEvent;
	import flash.events.MouseEvent;;
	
	// -- IMPORT NET
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	
	// -- IMPORT CAURINA TWEENER
	import caurina.transitions.Tweener;
	import caurina.transitions.properties.ColorShortcuts;
	
	
	
	public class ImageComponent extends MovieClip
	{
		private var objectLoader:Loader;
		private var Url:String="";
		private	var req:URLRequest;//url request variable
		private var _playerWidth:int = 790;
		private var _playerHeight:int = 712;
		public function ImageComponent()
		{
			
		}
		
		public function Set(w:int, h:int):void
		{
			
			_playerWidth = w;
			_playerHeight = h;
			
			this.mcBack.width = _playerWidth;
			this.mcBack.height = _playerHeight;
			this.mcBack.x = 0;
			this.mcBack.y = 0;
			
			return;
		}
		
		public function Resize()
		{
			
		}
		
		public function Load(url:String):void
		{
			Tweener.removeTweens(mcHolder);
			try{
				objectLoader.contentLoaderInfo.removeEventListener(Event.COMPLETE,onCompleteLoadImage);
				objectLoader = null;
				objectLoader.unload();
			}
			catch(e:Error)
			{
			}
			Tweener.addTween(mcHolder, { alpha:0,  time:.33, transition:"linear", onComplete: function()
																										{
																											while(mcHolder.numChildren)
																											{
																												mcHolder.removeChildAt(0);
																											}
																											objectLoader = new Loader();
																											objectLoader.contentLoaderInfo.addEventListener(Event.COMPLETE,onCompleteLoadImage);																						
																											objectLoader.load(new URLRequest(url));
																										}} );
			
		}
		
		private function onCompleteLoadImage(e:Event):void
		{
			try
			{
				this.mcHolder.scaleX = this.mcHolder.scaleY = 1;
				var bitmap:Bitmap = Bitmap(e.currentTarget.content);
				var m:Number=(mcBack.width)/bitmap.width;
				var n:Number=(mcBack.height)/bitmap.height;
				bitmap.width = bitmap.width*Math.max(n,m);
				bitmap.height = bitmap.height*Math.max(n,m);
				bitmap.x = 0;//(mcBack.width-bitmap.width)/2;
				bitmap.y = 0;//(mcBack.height-bitmap.height)/2;	
				bitmap.smoothing=true;
				mcHolder.addChild(bitmap);
				var mcBMP:BitmapData=new BitmapData(mcBack.width, mcBack.height, true, 0xFFFFFF);
				mcBMP.draw(mcHolder);
				var bmp:Bitmap=new Bitmap(mcBMP);
				bmp.smoothing=true;
				mcHolder.removeChild(bitmap);
				mcHolder.addChild(bmp);
				Tweener.addTween(mcHolder,{alpha:1, time:.33,transition:"linear" });
				mcHolder.x=mcBack.width/2 - mcHolder.width/2;
				mcHolder.y=(mcBack.height)/2 - mcHolder.height/2;
				var li:LoaderInfo = objectLoader.contentLoaderInfo;
				if(li.childAllowsParent && li.content is Bitmap){
				(li.content as Bitmap).bitmapData.dispose(); // remove bitmap from memory
				}
			}
			catch(e:Error)
			{
			}
		}
	}// CLOSE CLASS
}// CLOSE PACKAGE