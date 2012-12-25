//////////////////////////////////
// VIDEO PLAYER v3--------------//
// VIDEO COMPONENT -------------//
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
	import flash.events.NetStatusEvent;;
	
	// -- IMPORT NET
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	
	//- MEDIA
	import flash.media.Video;
	import flash.media.SoundChannel;
	import flash.media.SoundChannel;
	import flash.media.SoundTransform;
	
	//- NET
    import flash.net.NetConnection;
    import flash.net.NetStream;
	
	// -- IMPORT CAURINA TWEENER
	import caurina.transitions.Tweener;
	import caurina.transitions.properties.ColorShortcuts;
	
	
	
	public class VideoComponent extends MovieClip
	{
		private var Url:String="";
		private	var req:URLRequest;//url request variable
		private var _playerWidth:int = 790;
		private var _playerHeight:int = 712;
		private var _latestVolumeVal:Number = 0.7;//latest volume value - used for mute 
		private var _videoLoaded:Boolean = false;
		private var _isPlaying:Boolean = false;
		private var _isSeek:Boolean = false;
		private var nc:NetConnection = new NetConnection ();// net connection
		private var ns:NetStream;// net stream
		private var vid:Video;// video object
		private var videoLoadedTime:Number;//keeps the loaded video time
		private var realVideoHeight:Number;// real Video height
		private var realVideoWidth:Number;// real Video width
		private var totalVideoTime:Number;//total Video time
		public function VideoComponent()
		{
			
		}
		
		public function Set(w:int, h:int):void
		{
			//mcHolder.alpha=0;
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
			Url = url;
			LoadVideo();			
		}
		
		private function LoadVideo()
		{
			_videoLoaded = true;
			nc.connect(null);
			ns = new NetStream(nc);
			//creade new video
			vid = new Video();
	
			ns.client= new Object();
			//set buffer time
			ns.bufferTime=3;
			//play stream
			ns.play(Url);
			//get metadata
			ns.client.onMetaData = metaDataGather;	
			//pause stream
			ns.pause();
			vid.attachNetStream(ns);		
			vid.smoothing = true;
			vid.alpha = 0;
			//add video to video holder
			mcHolder.addChild(vid);
			//add event listener on net stream for events
			ns.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
			//set video volume
			ns.soundTransform = new SoundTransform(_latestVolumeVal);	
			//set video loade time interval		
			mcHolder.alpha=1;
			
			
			
		}
		
		private function netStatusHandler(e:NetStatusEvent) 
		{
			switch (e.info.code) 
			{
				case "NetStream.Play.StreamNotFound" :
					trace("Unable to locate video");
				break;
				case "NetStream.Play.Stop":
					//on video finished call function
					VideoFinished();				
				break;			
				case "NetStream.Seek.InvalidTime" :
					trace("invalid time");
				break;
				case "NetStream.Buffer.Full" :
						
					break;
				case "NetStream.Buffer.Flush" :
						
					break;
				case "NetStream.Buffer.Empty" :
						
					break;
			}		
		}
		
		private function metaDataGather(mt:Object):void 
		{
			//get real video height
			realVideoHeight = mt.height;
			//get real video width
			realVideoWidth = mt.width;		
			//get video time
			totalVideoTime = mt.duration;		
			//set movie scale
			setMovieScale();		
		}
		
		
		/*private function setMovieScale():void
		{		
			
			
			var n:Number=_playerWidth/realVideoWidth;
			var m:Number=_playerHeight/realVideoHeight;
			if(n>m)
			{
				vid.width =realVideoWidth;
				vid.scaleY =vid.scaleX;
			}
			if(n<m)
			{
				vid.height =realVideoHeight;
				vid.scaleX =vid.scaleY;
			}
			this.mcHolder.x = _playerWidth/2 - mcHolder.width/2;
			this.mcHolder.y = _playerHeight/2 - mcHolder.height/2;
			vid.alpha = 1;
	}*/
	
	private function setMovieScale():void
		{		
			var n:Number=_playerWidth/realVideoWidth;
			var m:Number=_playerHeight/realVideoHeight;
			/*if(n>m) // just in case you need to fill all player with correctly scaled video
			{
				vid.width = _playerWidth;
				vid.height = realVideoHeight * n;
			}
			if(n<=m)
			{
				vid.height = _playerHeight;
				vid.width = realVideoWidth * m;
			}*/
			if(n<=m) // we will fit video into the player
			{
				vid.width = _playerWidth;
				vid.height = realVideoHeight * n;
			}
			if(n>m)
			{
				vid.height = _playerHeight;
				vid.width = realVideoWidth * m;
			}
			this.mcHolder.x = _playerWidth/2 - mcHolder.width/2;
			this.mcHolder.y = _playerHeight/2 - mcHolder.height/2;
			vid.alpha = 1;
	}
	
	public function Play():void
	{
		if (!_videoLoaded) LoadVideo();
		if (_isPlaying)
		{
			_isPlaying = false;
			ns.pause();
		}
		else
		{
			if(mcHolder.alpha==0) mcHolder.alpha=1;
			_isPlaying = true;
			ns.resume();
		}
	}
	
	private function VideoFinished():void
	{		
		
		if(!_isSeek)
		{
			ns.pause();
			ns.seek(0);
			_isPlaying = false;
			mcHolder.alpha=0;
			MovieClip(this.parent).Check();
		}
		
	}
	
	public function get IsPlaying():Boolean
	{
		return _isPlaying;
	}
	
	public function get BytesLoaded():Number
	{
		return ns.bytesLoaded;
	}
	
	public function get BytesTotal():Number
	{
		return ns.bytesTotal;
	}
	
	public function get TotalVideoTime():Number
	{
		return totalVideoTime;
	}
	
	public function get VideoLoadedTime():Number
	{
		return (ns.bytesLoaded/ns.bytesTotal);
	}
	
	public function get Time():Number
	{
		return ns.time;
	}
	
	public function set Seek(sk:Number):void
	{
		ns.seek(sk);
	}
	
	public function get IsSeek():Boolean
	{
		return _isSeek;
	}
	
	public function set IsSeek(flag:Boolean):void
	{
		_isSeek=flag;
	}
	
	public function get Volume():Number
	{
		return _latestVolumeVal;
		
		
	}
	
	public function set Volume(vol:Number):void
	{
		_latestVolumeVal = vol;
		ns.soundTransform = new SoundTransform(_latestVolumeVal);
		
	}
		
	}// CLOSE CLASS
}// CLOSE PACKAGE