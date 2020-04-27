<?php
function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR. $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');

use OSS\OssClient;
use OSS\Core\OssException;

final class OSSWrapper extends OssClient {
	private $position = 0, $mode = '', $buffer;

	public function url_stat($path, $flags) {
		if(stripos(basename( $path ),'.')===false){
			$return = array('mode' => 16895);
		}else{
			self::__getURL($path);
			$info = self::getObjectMeta($this->url['host'], $this->url['path']);
			$return = $info->isOK() ? array(
				'mode' => 33279,
				'size' => $info->header['content-length'],
				'atime' => $info->header['info']['filetime'],
				'mtime' => $info->header['info']['filetime'],
				'ctime' => $info->header['info']['filetime']
			) : false;
		}
		//clearstatcache( true );
		return $return;
	}

	public function unlink($path) {
		self::__getURL($path);
		$info = self::deleteObject($this->url['host'], $this->url['path']);
		return $info->isOK();
	}
	public function mkdir($path, $mode, $options) {
		self::__getURL($path);
		$info = self::createObjectDir($this->url['host'], $this->url['path']);
		return $info->isOK();
	}
	public function rmdir($path) {
		self::__getURL($path);
		$info = self::deleteObject($this->url['host'], $this->url['path']);
		return $info->isOK();
	}
	public function dir_opendir($path, $options) {
		self::__getURL($path);

		if (($contents = self::readDir($path)) !== false) {
			$pathlen = strlen($this->url['path']);
			if (substr($this->url['path'], -1) == '/') $pathlen++;
			$this->buffer = array();
			foreach ($contents as $file) {
				if ($pathlen > 0) $file['name'] = substr($file['name'], $pathlen);
				$this->buffer[] = $file;
			}
			return true;
		}

		return false;
	}
	public function dir_readdir() {
		return (isset($this->buffer[$this->position])) ? $this->buffer[$this->position++]['name'] : false;
	}
	public function dir_rewinddir() {
		$this->position = 0;

	}
	public function dir_closedir() {
		$this->position = 0;
		unset($this->buffer);
	}
	public function stream_close() {
		if ($this->mode == 'w') {
			$options = array(
				'content-length'=> strlen($this->buffer)
			);
			self::putObject($this->url['host'], $this->url['path'], $this->buffer,null);
		}
		$this->position = 0;

		unset($this->buffer);
	}
	public function stream_stat() {

		if (is_object($this->buffer) && isset($this->buffer->headers)){
			return array(
				'size' => $this->buffer->headers['size'],
				'mtime' => $this->buffer->headers['time'],
				'ctime' => $this->buffer->headers['time']
			);
		}else{
			$info = self::getObjectMeta($this->url['host'], $this->url['path']);

			if($info->isOK()) return array('size' => $info->header['content-length'], 'atime' => $info->header['info']['filetime'], 'mtime' => $info->header['info']['filetime'], 'ctime' => $info->header['info']['filetime']);
		}
		return false;
	}
	public function stream_flush() {

		$this->position = 0;
		return true;
	}
	public function stream_open($path, $mode, $options, &$opened_path) {
		if (!in_array($mode, array('r', 'rb', 'w', 'wb'))) return false; // Mode not supported
		$this->mode = substr($mode, 0, 1);

		self::__getURL($path);
		$this->position = 0;
		if ($this->mode == 'r') {
			if (($this->buffer = self::getObject($this->url['host'], $this->url['path'])) !== false) {
				if (is_object($this->buffer->body)) $this->buffer->body = (string)$this->buffer->body;
			} else return false;
		}

		return true;
	}
	public function stream_read($count) {

		if ($this->mode !== 'r' && $this->buffer !== false) return false;
		$data = substr(is_object($this->buffer) ? $this->buffer->body : $this->buffer, $this->position, $count);
		$this->position += strlen($data);

		return $data;
	}
	public function stream_write($data) {
		if ($this->mode !== 'w') return 0;

		$left = substr($this->buffer, 0, $this->position);
		$right = substr($this->buffer, $this->position + strlen($data));

		$this->buffer = $left . $data . $right;

		$this->position += strlen($data);

		return strlen($data);

	}
	public function stream_tell() {
		return $this->position;
	}
	public function stream_eof() {
		return $this->position >= strlen(is_object($this->buffer) ? $this->buffer->body : $this->buffer);
	}
	public function stream_seek($offset, $whence) {

		switch ($whence) {
			case SEEK_SET:
                if ($offset < strlen($this->buffer->body) && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                } else return false;
            break;
            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                } else return false;
            break;
            case SEEK_END:
                $bytes = strlen($this->buffer->body);
                if ($bytes + $offset >= 0) {
                    $this->position = $bytes + $offset;
                    return true;
                } else return false;
            break;
            default: return false;
        }
    }
    private function __getURL($path) {
        $this->url = parse_url($path);
        if (!isset($this->url['scheme']) || $this->url['scheme'] !== 'oss') return $this->url;

        $this->url['path'] = isset($this->url['path']) ? substr($this->url['path'], 1) : '';

    }
}
stream_wrapper_register('oss', 'OSSWrapper');
//
// $rnd = md5(time());
// $file = 'oss://shijiechao/oss_upload_'.$rnd.'.txt';
// $try = file_put_contents($file, $rnd);
// if($try == strlen($rnd)){
//     $out = '写入成功';
//     $try = file_get_contents($file);
//     if($try == $rnd){
//         $out .= '读取成功';
//         $try = unlink($file);
//         if($try === true){
//             $out .= '删除成功';
//         }else{
//             $out = '删除失败';
//         }
//     }else{
//         $out = '读取失败';
//     }
// }else{
//     $out = '写入失败';
// }
// echo $out;
