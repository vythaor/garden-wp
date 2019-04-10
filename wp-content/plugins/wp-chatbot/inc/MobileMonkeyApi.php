<?php

class MobileMonkeyApi {

	private $option_prefix = 'mobilemonkey_';
	private $api_domain = 'https://api.mobilemonkey.com/';
	private $src = 'wordpress';
	private $pages = [];
	private $active_page = false;
	private $promoters = [];
	private $landing_page;
	private $env = true;
	private $pagination;
	private $contacts;
	private $plugin_name = 'wp-chatbot';
	
	private function getApiDomain(){
		return $this->api_domain;
	}

	private function getSrc(){
		return $this->src;
	}

	public function getOptionPrefix(){
		return $this->option_prefix;
	}

	private function setToken(){
		$token = filter_input( INPUT_GET, "auth_token", FILTER_SANITIZE_STRING );
		if ($token) {
			update_option( $this->option_prefix.'token', $token );
			return true;
		}
		return false;
	}

	private function getToken(){
		return get_option($this->option_prefix.'token');
	}

	private function setCompanyId(){
		$company_id = filter_input( INPUT_GET, "company_id", FILTER_SANITIZE_STRING );
		if ($company_id) {
			update_option( $this->option_prefix . 'company_id', $company_id );
			return true;
		}
		return false;
	}

	private function getCompanyId($connection_page_id){

		if($connection_page_id){
			$pages = $this->getPages();			
			foreach ($pages as $page) {
				if ($page['facebook_page_id']  && $connection_page_id == $page['remote_id']) {

					return $page['company_id'];
					break;
				}				
			}
		}		
		return get_option($this->option_prefix . 'company_id');
	}

	private function setActiveBotId($bot_id){
		update_option( $this->option_prefix . 'active_bot', $bot_id );
	}

	private function getActiveBotId(){
		return get_option( $this->option_prefix . 'active_bot' );
	}

	private function setActivePageId($page_id){
		update_option( $this->option_prefix . 'active_page_id', $page_id );
	}

	private function getActivePageId(){
		return get_option( $this->option_prefix . 'active_page_id');
	}

	private function setEnvironment($environment){
		update_option( $this->option_prefix . 'environment', $environment );
	}

	public function getEnvironment(){
		return get_option( $this->option_prefix . 'environment');
	}

	public function refreshSettingsPage(){
		echo "<script type='text/javascript'>
				var path = location.protocol + '//' + location.host + location.pathname + '?page=wp-chatbot';
		        window.location = path;
		        </script>";
	}

	public function connectLink(){
		$current_user = wp_get_current_user();

		if(!empty($current_user->user_email)){
			$user_email = $current_user->user_email;
		}else{
			$user_email = get_option('admin_email', '');
		}
		return $this->getApiDomain().'/wordpress/auth?callback="' . add_query_arg( [ 'page' => $this->plugin_name ], admin_url( 'admin.php' ) ) . '"&email=' . $user_email;
	}

	public function connectMobileMonkey(){
		if($this->setToken() && $this->setCompanyId()) {

			$this->getEnv();

			$this->sendUserEmail();

			$this->refreshSettingsPage();
		}
		return $this->getToken();
	}

	public function logoutMobilemonkey($reset = false){
		$logout = filter_input( INPUT_GET, "logout", FILTER_SANITIZE_STRING );
		if ( $logout || $reset) {
			delete_option( $this->option_prefix . 'token');
			delete_option( $this->option_prefix . 'company_id');
			delete_option( $this->option_prefix . 'active_page_id');		
			delete_option( $this->option_prefix . 'active_page_remote_id');
			delete_option( $this->option_prefix . 'active_bot');			

			$this->refreshSettingsPage();      
		}
	}
	
	public function connectPage(){		

		$pageId = filter_input( INPUT_GET, "connect", FILTER_SANITIZE_STRING );
		$pageName = filter_input( INPUT_GET, "page_name", FILTER_SANITIZE_STRING );

		if ($pageId && $pageName) {
			$args = [
				'timeout' => 10,
				'body' => json_encode([
					'remote_id' => $pageId,
					'company_id' => $this->getCompanyId($pageId),
					'name' => $pageName,
					'base_url' => get_site_url(),
					'src' => $this->getSrc(),
				]),
				'headers' => [
					'Authorization' => $this->getToken(),
					'Content-Type' => 'application/json; charset=utf-8',
				],
			];
			$response = wp_remote_post($this->getApiDomain() . '/api/facebook_pages', $args);
			$content = wp_remote_retrieve_body($response);
			$connect_response = json_decode($content);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				$this->renderNotice('API communication error. Please try again later.');
			} elseif (!empty($connect_response->success)) {
				if ($connect_response->facebook_page->remote_id) {

					if ($connect_response->facebook_page->square_picture) {
						update_option($this->option_prefix . 'active_page_image', $connect_response->facebook_page->square_picture);
					}

					$options = get_option('htcc_options', array());
					$options['fb_page_id'] = $connect_response->facebook_page->remote_id;

					if($connect_response->welcome_message){
						$options['fb_welcome_message'] = $connect_response->welcome_message;
					} else {
						$options['fb_welcome_message'] = '';
					}

					update_option('htcc_options', $options);

					$this->refreshSettingsPage();      

					return true;	
				}
				
			} elseif ($connect_response->error_code) {
				$this->renderNotice('Error code: ' . $connect_response->error_code);
				if (!empty($connect_response->errors)) {
					foreach ($connect_response->errors as $error) {
						$this->renderNotice('Error: ' . $error);
					}
				}
			} elseif (!empty($connect_response->errors)) {
				foreach ($connect_response->errors as $error) {
					$this->renderNotice('Error: ' . $error);
				}
			} else {
				$this->renderNotice('API communication error. Please try again later.');
			}
		}
		return false;
	}

	public function disconnectPage() {
		$pageId = filter_input( INPUT_GET, "disconnect", FILTER_SANITIZE_STRING );

		if ($pageId) {
			$args = [
				'timeout' => 10,
				'body' => json_encode([
					'src' => $this->getSrc(),
				]),
				'headers' => [
					'Authorization' => $this->getToken(),
					'Content-Type' => 'application/json; charset=utf-8',
				],
				'method' => 'DELETE',
			];
			$response = wp_remote_request($this->getApiDomain() . '/api/facebook_pages/' . $pageId, $args);
			$content = wp_remote_retrieve_body($response);
			if (empty($content)) {

				$this->active_page = false;

				$options = get_option('htcc_options', array());
				$options['fb_page_id'] = '';
				$options['fb_welcome_message'] = '';
				update_option('htcc_options', $options);

				delete_option($this->option_prefix . 'active_page_id');				
				delete_option($this->option_prefix . 'active_page_remote_id');
				delete_option($this->option_prefix . 'active_bot');				
				
				$this->refreshSettingsPage();		

				return true;
				
			} elseif (isset($response["response"]["code"]) && $response["response"]["code"] == 422) {
				$this->renderNotice('The page is not connected!');
			} else {
				$json_response = json_decode($content);
				if (json_last_error() === JSON_ERROR_NONE && !empty($json_response->errors)) {
					foreach ($json_response->errors as $error) {
						$this->renderNotice('Error: ' . $error);
					}
				}
			}
			return false;
		}				
	}

	public function getPages($reset = false) {
		if (!empty($this->pages) && !$reset) {
			return $this->pages;
		}

		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => $this->getToken()
			],
		];
		$pagesObj = NULL;
		$pages    = [];
		$response = wp_remote_get( $this->getApiDomain() . '/api/facebook_pages/available_options?src=' . $this->getSrc(), $args );	
		$content  = wp_remote_retrieve_body( $response );
		
		if ( ! empty( $content ) ) {
			$pagesObj = json_decode( $content );
		
			if ( empty( $pagesObj->errors ) ) {
			
				foreach ( $pagesObj->data as $page ) {
					$p = [
						'name' => $page->name,
						'remote_id' => $page->remote_id,
						'id' => $page->facebook_page_id,
						'bot_id' => $page->bot_id,
            			'bot_kind' => $page->bot_kind,
            			'company_id' => $page->company_id,
						'path' => add_query_arg( [
							'page' => $this->plugin_name,
							'connect' => $page->remote_id,
							'page_name' => $page->name
						], admin_url( 'admin.php' ) ),
					];
					
					$pages[] = $p;
				}			
			} else {
				$this->logoutMobilemonkey(true);
			}
		}

		$this->pages = $pages;

		return $pages;
	}

	public function getActivePage($reset = false) {
		
		if(!$reset && !empty($this->active_page)){
			return $this->active_page;			
		}
						
		$activePage = [];
		$pages = $this->getPages();

		$options = get_option('htcc_options', array());
		$active_remote_page_id  = $options['fb_page_id'];
		
		foreach ( $pages as $page ) {
			
			if($active_remote_page_id == $page['remote_id']){
				$activePage['remote_id'] = $page['remote_id'];
				$activePage['bot_id'] = $page['bot_id'];
				$activePage['name'] = $page['name'];
				$activePage['id'] = $page['facebook_page_id'];
				$activePage['path'] = add_query_arg([
					'page' => $this->plugin_name,
					'disconnect' => $page['id'],
				], admin_url('admin.php'));

				update_option($this->option_prefix . 'active_page_remote_id', $page['remote_id']);
				$this->setActivePageId($page['id']);
				$this->setActiveBotId($page['bot_id']);

				break;
			}
		}
		
		$this->active_page = $activePage;
		return $activePage;
	}

	public function sendUserEmail(){
		$current_user = wp_get_current_user();
		
		if(!empty($current_user->user_email)){
			$user_email = $current_user->user_email;
		}else{
			$user_email = get_option('admin_email', '');
		}
		
		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => $this->getToken(),
				'Content-Type' => 'application/json',
			],
			'body' => json_encode([
				'user' => [
					"wp_email" => $user_email,
				],				
				'src' => $this->getSrc(),
			]),
			'method' => 'PUT',
		];
			
		$response = wp_remote_request($this->getApiDomain() . '/api/user/', $args);		
		if(isset($response["response"]["code"]) && $response["response"]["code"] == 200){
			// Email successfully sent
		}else{
			$this->renderNotice('API communication error, unable to update. Please try again later.');
		}
	}

	public function getWelcomeMessage($remote_id) {
		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => $this->getToken(),
				'Content-Type' => 'application/json',
			]
		];

		$response = wp_remote_get($this->getApiDomain() . '/api/wordpress_settings/welcome_message?fb_page_remote_id=' . $remote_id, $args);
		$welcome_message = wp_remote_retrieve_body($response);

		if (isset($response["response"]["code"]) && $response["response"]["code"] == 200) {
			return str_replace('"', '', $welcome_message);
		}
	}

	public function updateWelcomeMessage($new_welcome_message, $fb_page_id) {
		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => $this->getToken(),
				'Content-Type' => 'application/json',
			],
			'body' => json_encode([
				'body' => $new_welcome_message,
				'fb_page_remote_id' => $fb_page_id
			]),
			'method' => 'PUT',
		];

		$response = wp_remote_request( $this->getApiDomain() . '/api/wordpress_settings/welcome_message', $args);

		if (isset($response["response"]["code"]) && $response["response"]["code"] == 200) {
			// Succesfully updated welcome message for $options['fb_page_id'] page
		} else {
			$this->renderNotice('API communication error, unable to update. Please try again later.');
		}
		
	}

	public function getEnv() {		

		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => $this->getToken(),
			],
		];
	
		$response = wp_remote_get( $this->getApiDomain() . '/api/env/', $args );
		$content  = wp_remote_retrieve_body( $response );
		if ( !empty( $content ) ) {

			$env = json_decode( $content );
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				$this->renderNotice('API communication error. Please try again later.');
			}
			elseif ( !empty( $env->errors ) ) {
				$this->renderNotice('API communication error. Please try again later.');
			}
			else {			
				$this->env = $env;

				$options = get_option('htcc_options', array());
				$options['fb_app_id'] = $this->env->fb_app_id;
				update_option('htcc_options', $options);	

				$this->setEnvironment($env);

				return $env;
			}
		}

		return false;
	}

    private function renderNotice($text)
    {
        $setting_page_args = [
            'text' => $text,
        ];
        HT_CC::view('ht-cc-admin-fb-button-notice', $setting_page_args);
    }

    public function debug(){
        $options = [];
        $options['token'] = get_option( $this->option_prefix . 'token');
        $options['company_id'] = get_option( $this->option_prefix . 'company_id');
        $options['active_page_id'] = get_option( $this->option_prefix . 'active_page_id');
        $options['active_page_remote_id'] = get_option( $this->option_prefix . 'active_page_remote_id');
        $options['active_bot'] = get_option( $this->option_prefix . 'active_bot');
        $options['environment'] = get_option( $this->option_prefix . 'environment');
        $options['htcc_options'] = get_option('htcc_options');
        return var_dump($options);
    }
    private function setContacts($data){
        $this->contacts = $data;
    }

    public function getContacts(){
        if(empty($this->contacts)){
            $this->getData();
        }
        return $this->contacts;
    }

    private function setPagination($data){
        $this->pagination = $data;
    }

    public function getPagination(){
        if(empty($this->pagination)){
            $this->getData();
        }
        return $this->pagination;
    }

    private function getArgs(){
        $get = [
            'page' => 1,
            'pre_page' => 25,
            'sort_column' => 'created_at',
            'sort_direction' => 'desc',
        ];

        $paged = filter_input( INPUT_GET, "paged", FILTER_SANITIZE_STRING );
        if(!empty($paged)){
            $get['page'] = $paged;
        }

        $orderby = filter_input( INPUT_GET, "orderby", FILTER_SANITIZE_STRING );
        if(!empty($orderby)){
            $get['sort_column'] = $orderby;
        }

        $order = filter_input( INPUT_GET, "order", FILTER_SANITIZE_STRING );
        if(!empty($order)){
            $get['sort_direction'] = $order;
        }

        return $get;

    }

    private function getData(){
        $get = $this->getArgs();

        $args = [
            'timeout' => 10,
            'headers' => [
                'Authorization' => $this->getToken(),
            ],
        ];

        $activePageId = $this->getActivePageId();

        $response = wp_remote_get( $this->getApiDomain() . 'api/facebook_pages/'.$activePageId.'/contacts?page='.$get['page'].'&per_page='.$get['pre_page'].'&sort_column='.$get['sort_column'].'&sort_direction='.$get['sort_direction'].'&src='.$this->getSrc(), $args );
        $content  = wp_remote_retrieve_body( $response );
        if ( ! empty( $content ) ) {
            $contacts = json_decode( $content );
            $this->setContacts($contacts->contacts);
            $this->setPagination($contacts->pagination);
        }
    }
}