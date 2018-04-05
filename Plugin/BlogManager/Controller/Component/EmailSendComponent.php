<?php
App::uses('Component', 'Controller');
App::uses('Multibyte', 'I18n');
App::uses('CakeEmail', 'Network/Email');
	class EmailSendComponent extends Component {
		public $layout = 'default';
		public $template = null;
		public $sendAs = 'text';
		public $delivery = 'mail';
		public $charset = 'utf-8';
		public $attachments = array();
		public $lineFeed = PHP_EOL;
		public $additionalParams = null;
		public $xMailer = 'CakePHP Email Component';
		public $filePaths = array();
		public $textMessage = null;
		public $htmlMessage = null;
		static $smtpOptions =array();
		
		
	public function sendmailtoadmin($Mailcontent = array()) {
		
		
		
		
		$Email = new CakeEmail();
		$Email->charset = $this->charset;
		$Email->headerCharset = $this->charset;
		$Email->from($from);
		if (!empty($to)) {
			$Email->to($to);
		}
		if (!empty($cc)) {
			$Email->cc($cc);
		}
		
		$Email->subject($this->subject);
		$headers = array('X-Mailer' => $this->xMailer);
		foreach ($this->headers as $key => $value) {
			$headers['X-' . $key] = $value;
		}
		$Email->setHeaders($headers);
		if ($template) {
			$this->template = $template;
		}
		if ($layout) {
			$this->layout = $layout;
		}
		$Email->template($this->template, $this->layout)->viewVars($this->_controller->viewVars)->emailFormat($this->sendAs);
		if (!empty($this->attachments)) {
			$Email->attachments($this->_formatAttachFiles());
		}
		$Email->transport(ucfirst($this->delivery));
		if ($this->delivery === 'mail') {
			$Email->config(array('eol' => $this->lineFeed, 'additionalParameters' => $this->additionalParams));
		}elseif ($this->delivery === 'smtp') {
			
			$Email->config($this->smtpOptions);
		} else {
			$Email->config(array());
		}
		$sent = $Email->send($content);
		$this->htmlMessage = $Email->message(CakeEmail::MESSAGE_HTML);
		if (empty($this->htmlMessage)) {
		$this->htmlMessage = null;
		}
		$this->textMessage = $Email->message(CakeEmail::MESSAGE_TEXT);
		if (empty($this->textMessage)) {
			$this->textMessage = null;
		}
		$this->_header = array();
		$this->_message = array();
		return $sent;
	}
	
	public function reset() {
		$this->template = null;
		$this->to = array();
		$this->from = null;
		$this->return = null;
		$this->cc = array();
		$this->subject = null;
		$this->additionalParams = null;
		$this->date = null;
		$this->attachments = array();
		$this->htmlMessage = null;
		$this->textMessage = null;
		$this->messageId = true;
		$this->delivery = 'mail';
	}

	
}
