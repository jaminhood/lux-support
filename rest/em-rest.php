<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxREST')) {
  class LuxRest
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
      $this->lux_rest_init();
    }

    public function lux_rest_init()
    {
      add_action('rest_api_init', [$this, 'lux_get_bank_detail_route']);

      add_action('rest_api_init', [$this, 'lux_update_bank_detail_route']);

      add_action('rest_api_init', [$this, 'lux_get_transaction_pin_route']);

      add_action('rest_api_init', [$this, 'lux_confirm_transaction_pin_route']);

      add_action('rest_api_init', [$this, 'lux_set_transaction_pin_route']);

      add_action('rest_api_init', [$this, 'lux_update_transaction_pin_route']);

      add_action('rest_api_init', [$this, 'lux_users_registration_route']);

      add_action('rest_api_init', [$this, 'lux_get_top_assets_route']);

      add_action('rest_api_init', [$this, 'lux_get_top_news_route']);

      add_action('rest_api_init', [$this, 'lux_delete_user_route']);

      add_action('rest_api_init', [$this, 'lux_get_transaction_route']);

      add_action('rest_api_init', [$this, 'lux_get_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_get_single_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_close_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_open_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_open_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_delete_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_get_all_chats_tickets_route']);

      add_action('rest_api_init', [$this, 'lux_get_recent_chats_tickets_route']);

      add_action('rest_api_init', [$this, 'lux_get_referrals_route']);

      add_action('rest_api_init', [$this, 'lux_get_asset_route']);

      add_action('rest_api_init', [$this, 'lux_send_buy_asset_route']);

      add_action('rest_api_init', [$this, 'lux_get_giftcard_categories_route']);

      add_action('rest_api_init', [$this, 'lux_get_giftcard_category_route']);

      add_action('rest_api_init', [$this, 'lux_get_giftcard_sub_categories_route']);

      add_action('rest_api_init', [$this, 'lux_create_support_ticket_route']);

      add_action('rest_api_init', [$this, 'lux_get_customer_tickets_route']);

      add_action('rest_api_init', [$this, 'lux_open_chat_route']);

      add_action('rest_api_init', [$this, 'lux_send_chat_route']);

      add_action('rest_api_init', [$this, 'lux_sell_giftcard_route']);

      add_action('rest_api_init', [$this, 'lux_set_device_token_route']);

      add_action('rest_api_init', [$this, 'lux_set_withdrawal_route']);

      add_action('rest_api_init', [$this, 'lux_get_fetch_statement_route']);
    }

    public function lux_permit_customers()
    {
      $user = wp_get_current_user();
      if (!in_array("customer", $user->roles)) return false;
      return true;
    }

    public function lux_get_bank_detail_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_bank_detail'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'bank-detail/get', $args);
    }

    public function lux_get_bank_detail($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $bankDetails = $this->lux_dbh->lux_get_bank_details($id);

          $response = new WP_REST_Response($bankDetails);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_transaction_pin_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_transaction_pin'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'transaction-pin/get', $args);
    }

    public function lux_get_transaction_pin($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $pin = $this->lux_dbh->lux_get_transaction_pin($id);
          $response = new WP_REST_Response($pin->transaction_pin == $request->get_param('pin'));
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to get transaction pin - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_confirm_transaction_pin_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_confirm_transaction_pin'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'transaction-pin/confirm', $args);
    }

    public function lux_confirm_transaction_pin($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $pin = $this->lux_dbh->lux_get_transaction_pin($id);
          $check = false;

          if (!empty($pin)) {
            $check = true;
          }

          $response = new WP_REST_Response($check);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to confirm transaction pin - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_set_transaction_pin_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_set_transaction_pin'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'transaction-pin/set', $args);
    }

    public function lux_set_transaction_pin($request)
    {
      if (isset($request['id'])) {
        try {
          $input_data = array(
            'customer_id'           => sanitize_text_field($request['id']),
            'transaction_pin'       => sanitize_text_field($request['pin']),
          );

          if ($this->lux_dbh->lux_set_transaction_pin($input_data)) {
            $response = new WP_REST_Response('Pin set successfully');
          } else {
            $response = new WP_REST_Response('Pin not set');
          }
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to set transaction pin - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_update_transaction_pin_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_update_transaction_pin'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'transaction-pin/update', $args);
    }

    public function lux_update_transaction_pin($request)
    {
      if (isset($request['id'])) {
        try {
          $input_data = array(
            'transaction_pin'       => sanitize_text_field($request['pin']),
          );

          if ($this->lux_dbh->lux_update_transaction_pin($input_data, $request['id'], $request['prev'])) {
            $response = new WP_REST_Response('Pin updated successfully');
          } else {
            $response = new WP_REST_Response('Error updating pin');
          }

          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update transaction pin - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_update_bank_detail_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_update_bank_detail'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'bank-details/update', $args);
    }

    public function lux_update_bank_detail($request)
    {
      if (isset($request['id'])) {
        try {
          $bank_name = sanitize_text_field($request['bankName']);
          $account_number = sanitize_text_field($request['bankAccountNumber']);
          $account_name = sanitize_text_field($request['bankAccountName']);
          # === Turn request into array
          $details = array(
            'bankName' => $bank_name,
            'bankAccountNumber' => $account_number,
            'bankAccountName' => $account_name
          );

          $where = ['id' => $request['id']];

          $this->lux_dbh->lux_update_bank_details($details, $where);

          $response = new WP_REST_Response('Bank details updated successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_top_assets_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_top_assets'],
        // 'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'top-assets/get', $args);
    }

    public function lux_get_top_assets()
    {
      try {
        $assets = $this->lux_dbh->lux_get_top_assets();
        $data = [];

        foreach ($assets as $asset) {
          switch ($asset->asset_type) {
            case 0:
              $curr = hid_ex_m_get_e_currency_data($asset->asset_id);
              $data[] = array(
                "id" => $curr->id,
                "name" => $curr->name,
                "short_name" => $curr->short_name,
                "icon" => wp_get_attachment_url($curr->icon),
                "buying_price" => $curr->buying_price,
                "selling_price" => $curr->selling_price,
              );
              break;
            case 1:
              $curr = hid_ex_m_get_crypto_currency_data($asset->asset_id);
              $data[] = array(
                "id" => $curr->id,
                "name" => $curr->name,
                "short_name" => $curr->short_name,
                "icon" => wp_get_attachment_url($curr->icon),
                "buying_price" => $curr->buying_price,
                "selling_price" => $curr->selling_price,
              );
              break;
            case 2:
              global $wpdb;
              $table_name = $wpdb->prefix . 'hid_ex_m_giftcards';
              $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$asset->asset_id'");

              if ($result) {
                $curr = $result[0];

                $data[] = array(
                  "id" => $curr->id,
                  "name" => $curr->name,
                  "short_name" => $curr->short_name,
                  "icon" => wp_get_attachment_url($curr->icon),
                  "buying_price" => $curr->buying_price,
                  "selling_price" => $curr->selling_price,
                );
              }

              break;
          }
        }

        $topAssets = $data;

        $response = new WP_REST_Response($topAssets);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_top_news_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_top_news'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'top-news/get', $args);
    }

    public function lux_get_top_news()
    {
      try {
        $allNews = $this->lux_dbh->lux_get_news();
        $results = [];

        foreach ($allNews as $news) {
          $results[] = [
            "id" => $news->id,
            "title" => $news->title,
            "image" => wp_get_attachment_url($news->newsPicture),
            "date" => $news->dateAdded
          ];
        }

        $response = new WP_REST_Response($results);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }

    public function lux_delete_user_route()
    {
      $args = [
        'methods'  => 'DELETE',
        'callback' => [$this, 'lux_delete_user'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'users/delete', $args);
    }

    public function lux_delete_user($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $user = get_user_by('id', $id);
          $role = $user->roles;

          if ($role[0] !== 'customer') {
            return new WP_Error(
              'not customer', # code
              'This user is not a customer', # message
              array('status' => 400) # status
            );
          }

          wp_delete_user($id);

          $response = new WP_REST_Response('user deleted successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_transaction_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_transaction'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'transaction/get', $args);
    }

    public function lux_get_transaction($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $user_wallet = hid_ex_m_wallet_page_data($id);
          $all_transactions = $user_wallet['all_transactions'];

          $response = new WP_REST_Response($all_transactions);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_support_ticket_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_support_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'tickets/get', $args);
    }

    public function lux_get_support_ticket()
    {
      try {
        $all_tickets = hid_ex_m_get_all_support_tickets();

        $response = new WP_REST_Response($all_tickets);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_single_support_ticket_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_single_support_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/get', $args);
    }

    public function lux_get_single_support_ticket($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $ticket = hid_ex_m_get_single_ticket_data($id);

          $response = new WP_REST_Response($ticket);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_close_support_ticket_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_close_support_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/close', $args);
    }

    public function lux_close_support_ticket($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $where = ['id' => $id];
          hid_ex_m_mark_support_ticket_as_close($where);

          $response = new WP_REST_Response('Ticket closed successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_open_support_ticket_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_open_support_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/open', $args);
    }

    public function lux_open_support_ticket($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $where = ['id' => $id];
          hid_ex_m_reopen_support_ticket($where);

          $response = new WP_REST_Response('Ticket opened successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_update_ticket_activity_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_update_ticket_activity'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/update/activity', $args);
    }

    public function lux_update_ticket_activity($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          hid_ex_m_update_last_activity($id);

          $response = new WP_REST_Response('Ticket last activity updated successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_delete_ticket_route()
    {
      $args = [
        'methods'  => 'DELETE',
        'callback' => [$this, 'lux_delete_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/delete', $args);
    }

    public function lux_delete_ticket($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          hid_ex_m_delete_support_ticket($id);

          $response = new WP_REST_Response('Ticket deleted successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_all_chats_tickets_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_all_chats_tickets'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/chats/all', $args);
    }

    public function lux_get_all_chats_tickets($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $chats = hid_ex_m_get_all_support_chat($id);

          $response = new WP_REST_Response($chats);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_recent_chats_tickets_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_recent_chats_tickets'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/chats/recent', $args);
    }

    public function lux_get_recent_chats_tickets($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          $time = $request->get_param('time');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }
          if (!$time) {
            return new WP_Error(
              'Time not found', # code
              'You forgot to attach the time', # message
              array('status' => 400) # status
            );
          }

          $chats = hid_ex_m_get_recent_support_chat_data($time, $id);

          $response = new WP_REST_Response($chats);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_referrals_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_referrals'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'referral/get', $args);
    }

    public function lux_get_referrals($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }
          $referral = $this->lux_dbh->lux_get_referral($id);

          $response = new WP_REST_Response($referral);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_asset_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_asset'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'assets/get', $args);
    }

    public function lux_get_asset($request)
    {
      if (isset($request['asset-type'])) {
        try {
          $type = sanitize_text_field($request['asset-type']);
          $asset_id = '';
          $data = [];

          if ($type == 1) {
            $data = hid_ex_m_get_all_e_currency_assets();
            $asset_id = 1;
          } else if ($type == 2) {
            $data = hid_ex_m_get_all_crypto_currency_assets();
            $asset_id = 2;
          }

          if ($data != 0) {
            foreach ($data as $asset) {
              $asset->icon = wp_get_attachment_url($asset->icon);
              $barcode = $this->lux_dbh->lux_get_currency_barcode($asset_id, $asset->id);
              $asset->barcode = wp_get_attachment_url($barcode->barcode);
            }
          }

          $response = new WP_REST_Response($data);
          $response->set_status(200);

          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'unknown error occured', // code
            'an unknown error occured while trying to fetch assets', // data
            array('status' => 400) // status
          );
        }
      } else {
        return new WP_Error(
          'error occured', // code
          'missing field : asset-type', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_send_buy_asset_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_send_buy_asset'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'buy-asset', $args);
    }

    public function lux_send_buy_asset($request)
    {
      if (isset($request['id'])) {
        try {
          $input_data = array(
            'customer_id'           => $request['id'],
            'asset_type'            => $request['asset_type'],
            'asset_id'              => $request['asset_id'],
            'quantity'              => $request['quantity'],
            'fee'                   => $request['amount_to_receive'],
            'proof_of_payment'      => 0,
            'sending_instructions'  => $request['sending_instructions'],
            'order_status'          => 1
          );

          $this->lux_dbh->lux_create_new_buy_order($input_data);

          $input_data = array(
            'customer_id' => $request['id'],
            'transaction_type' => 2,
            'amount' => $request['amount_to_receive'],
            'mode'  => $request['asset_type'],
            'details'   => 'Buy Order',
            'proof_of_payment' => 0,
            'sending_instructions'  => $request['sending_instructions'],
            'transaction_status'    => 1
          );

          $this->lux_dbh->lux_create_new_wallet_transaction($input_data);

          $response = new WP_REST_Response('Asset bought successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_giftcard_category_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_giftcard_category'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'giftcard/category/all', $args);
    }

    public function lux_get_giftcard_category()
    {
      try {

        $categories = $this->lux_dbh->lux_get_all_giftcard_categories();

        $response = new WP_REST_Response($categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }

    public function lux_get_giftcard_categories_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_giftcard_categories'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'giftcard/category/get', $args);
    }

    public function lux_get_giftcard_categories($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $categories = $this->lux_dbh->lux_get_giftcard_category_data($id);

          $response = new WP_REST_Response($categories);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'error occured', // code
          'missing field : asset-type', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_get_giftcard_sub_categories_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_giftcard_sub_categories'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'giftcard/sub-category/all', $args);
    }

    public function lux_get_giftcard_sub_categories($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');

          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $categories = $this->lux_dbh->lux_get_giftcard_sub_category_data_with_category($id);

          $response = new WP_REST_Response($categories);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'error occured', // code
          'missing field : asset-type', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_users_registration_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_users_registration']
      ];

      register_rest_route('em', 'user/register', $args);
    }

    public function lux_users_registration($request)
    {

      if (isset($request['first-name']) && isset($request['last-name']) && isset($request['email']) && isset($request['phone'])  && isset($request['password']) && isset($request['username'])) {

        $email = sanitize_email($request['email']);
        $username = sanitize_text_field($request['username']);
        $phone = sanitize_text_field($request['phone']);
        $password = sanitize_text_field($request['password']);
        $first_name = sanitize_text_field($request['first-name']);
        $last_name = sanitize_text_field($request['last-name']);
        $code = sanitize_text_field($request['referral-code']);

        if (email_exists($email)) {

          return new WP_Error(
            'email already exists', // code
            'this email address have been used by another user. kindly provide another', // data
            array('status' => 401) // status
          );
        }

        if (username_exists($username)) {

          return new WP_Error(
            'username already exists', // code
            'this username address have been used by another user. kindly provide another', // data
            array('status' => 401) // status
          );
        }

        $this->lux_dbh->lux_create_new_customer(
          $first_name,
          $last_name,
          $email,
          $phone,
          $password,
          $username,
          $code
        );

        $response = new WP_REST_Response('user created successfully');
        $response->set_status(200);

        return $response;
      } else {

        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for customer registration', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_create_support_ticket_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_create_support_ticket'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/create', $args);
    }

    public function lux_create_support_ticket($request)
    {
      if (isset($request['ticket-title']) && isset($request['ticket-details']) && isset($request['customer-id']) && isset($request['customer-name'])) {
        $input = array(
          'title' => $request['ticket-title'],
          'details'    => $request['ticket-details'],
          'customer'      => $request['customer-id'],
          'ticket_status' => 1,
          'requester' => $request['customer-name']
        );

        hid_ex_m_create_new_support_ticket($input);
        $response = new WP_REST_Response('Ticket created successfully');
        $response->set_status(200);
        return $response;
      } else {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Ticket Creation', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_get_customer_tickets_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_customer_tickets'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'ticket/customer', $args);
    }

    public function lux_get_customer_tickets($request)
    {
      if (isset($request['id'])) {
        try {
          $id = $request->get_param('id');
          if (!is_numeric($id)) {
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          }

          $tickets = hid_ex_m_get_customer_support_tickets($id);

          $response = new WP_REST_Response($tickets);
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to get User's Ticket - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process => id', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_open_chat_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_open_chat'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'chat/open', $args);
    }

    public function lux_open_chat($request)
    {
      if (isset($request['ticket-id'])) {

        $all_chats = hid_ex_m_get_all_support_chat($request['ticket-id']);

        if ($all_chats != 0) {
          foreach ($all_chats as $message) {
            if ($message->attachment) {
              $message->attachment_url = wp_get_attachment_url($message->attachment);
            }
          }
        }

        $response = new WP_REST_Response($all_chats);
        $response->set_status(200);
        return $response;
      } else {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Ticket Creation => ticket-id', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_send_chat_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_send_chat'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'chat/send', $args);
    }

    public function lux_send_chat($request)
    {
      if (isset($request['ticket-id']) && isset($request['customer-name']) && isset($request['chat-text'])) {
        $data = 0;

        $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

        if (in_array($_FILES['file']['type'], $arr_img_ext)) {
          $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

          $type = '';
          if (!empty($upload['type'])) {
            $type = $upload['type'];
          } else {
            $mime = wp_check_filetype($upload['file']);
            if ($mime) {
              $type = $mime['type'];
            }
          }

          $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
          $data = wp_insert_attachment($attachment, $upload['file']);
          wp_update_attachment_metadata($data, wp_generate_attachment_metadata($data, $upload['file']));
        }

        hid_ex_m_create_new_support_chat(array(
          'sender' => $request['customer-name'],
          'message' => $request['chat-text'],
          'attachment' => $data,
          'ticket' => $request['ticket-id']
        ));

        $response = new WP_REST_Response('Chat sent');
        $response->set_status(200);
        return $response;
      } else {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Sending Chat => customer-name, chat-text, ticket-id', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_sell_giftcard_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_sell_giftcard'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'sell-giftcard', $args);
    }

    public function lux_sell_giftcard($request)
    {
      if (isset($request['asset_id']) && isset($request['quantity']) && isset($_FILES['file'])) {
        try {
          $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

          if (in_array($_FILES['file']['type'], $arr_img_ext)) {
            $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
            $type = '';

            if (!empty($upload['type'])) {
              $type = $upload['type'];
            } else {
              $mime = wp_check_filetype($upload['file']);

              if ($mime) {
                $type = $mime['type'];
              }
            }

            $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
            $data = wp_insert_attachment($attachment, $upload['file']);

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            wp_update_attachment_metadata($data, wp_generate_attachment_metadata($data, $upload['file']));
          } else {
            return new WP_Error(
              'error processing order', // code
              "error processing image", // data
              array('status' => 400) // status
            );
          }

          $id = $request['asset_id'];
          $asset = $this->lux_dbh->lux_get_giftcard_sub_category_data($id);
          $quantity_san = sanitize_text_field($request['quantity']);
          $price = $quantity_san * $asset->rate;

          $input_data = array(
            'customer_id' => get_current_user_id(),
            'asset_id'      => $id,
            'quantity' => $quantity_san,
            'price' => $price,
            'card_picture' => $data,
            'order_status'  => 1
          );

          $this->lux_dbh->lux_create_new_giftcard_order($input_data);

          $response = new WP_REST_Response('giftcard processed successfully');
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {

          return new WP_Error(
            'error processing order', // code
            "an error occured while trying to process the giftcard order - $th", // data
            array('status' => 400) // status
          );
        }
      } else {

        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for process', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_set_device_token_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_set_device_token'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'set-device-token', $args);
    }

    public function lux_set_device_token($request)
    {
      if (isset($request['device-token'])) {
        try {
          $token = sanitize_text_field($request['device-token']);

          if ($this->lux_dbh->lux_set_device_token($token)) {
            $response = new WP_REST_Response('Device Token set successfully');
          } else {
            $response = new WP_REST_Response('Device Token not set');
          }
          $response->set_status(200);
          return $response;
        } catch (\Throwable $th) {
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to set transaction Device Token - $th", # data
            array('status' => 400) # status
          );
        }
      } else {
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      }
    }

    public function lux_set_withdrawal_route()
    {
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'lux_set_withdrawal'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'make-withdrawal', $args);
    }

    public function lux_set_withdrawal($request)
    {
      if (isset($request['amount-to-withdraw']) && isset($request['sending-instructions'])) {

        if (hid_ex_m_get_withdrawal_status(get_current_user_id()) == 0) {
          return new WP_Error(
            'error occured', // code
            'withdrawal disabled for this user', // data
            array('status' => 400) // status
          );
        }

        if ($request['amount-to-withdraw'] < 500) {
          return new WP_Error(
            'error occured', // code
            'the amount to withdraw is not up to the minimum which is 500 naira', // data
            array('status' => 400) // status
          );
        }

        try {

          $current_balance = hid_ex_m_get_account_balance(get_current_user_id());
          $withdrawable_amount = $current_balance - 100;

          if ($request['amount-to-withdraw'] < $withdrawable_amount) {

            $input_data = array(
              'customer_id' => get_current_user_id(),
              'transaction_type' => 2,
              'amount' => intval($request['amount-to-withdraw']) + 50,
              'mode'  => 0,
              'details'   => "Withdrawal Request from Mobile App",
              'proof_of_payment' => 0,
              'sending_instructions'  => $request['sending-instructions'],
              'transaction_status'    => 1
            );

            $this->lux_dbh->lux_create_new_wallet_transaction($input_data);

            $response = new WP_REST_Response("Withdrawal Request Submitted Successfully");
            $response->set_status(200);
            return $response;
          } else {
            return new WP_Error(
              'error occured', // code
              'insufficient balance', // data
              array('status' => 400) // status
            );
          }
        } catch (\Throwable $th) {
          return new WP_Error(
            'unknown error occured', // code
            'an unknown error occured while trying to process withdrawals', // data
            array('status' => 400) // status
          );
        }
      } else {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted', // data
          array('status' => 400) // status
        );
      }
    }

    public function lux_get_fetch_statement_route()
    {
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'lux_get_fetch_statement'],
        'permission_callback' => [$this, 'lux_permit_customers']
      ];

      register_rest_route('em', 'fetch-statement', $args);
    }

    public function lux_get_fetch_statement($request)
    {
      if (isset($request['order'])) {
        $order = sanitize_text_field($request['order']);
        $customer_id = get_current_user_id();

        $output_data = [];

        if ($order == 1) {
          $output_data = $this->lux_dbh->lux_get_buy_orders();

          if (!empty($output_data)) {
            foreach ($output_data as $single) {
              $single->quantity = floatval($single->quantity);
              $single->amount = floatval($single->fee);

              $type = hid_ex_m_get_asset_type($single->asset_type);
              $single->type = $type;

              $asset = hid_ex_m_get_asset_full_name($single->asset_type, $single->asset_id);
              $single->asset = $asset;

              $single->snapshot = wp_get_attachment_url($single->proof_of_payment);

              $single->status = LuxUtils::lux_get_order_status($single->order_status);

              unset($single->order_status);
              unset($single->id);
              unset($single->customer_id);
              unset($single->quantity);
              unset($single->asset_id);
              unset($single->asset_type);
              unset($single->proof_of_payment);
              unset($single->fee);
            }
          }
        } else if ($order == 2) {

          $output_data = $this->lux_dbh->lux_get_sell_orders();

          if (!empty($output_data)) {
            foreach ($output_data as $single) {


              $single->quantity = floatval($single->quantity_sold);
              $single->amount = floatval($single->amount_to_recieve);

              $type = hid_ex_m_get_asset_type($single->asset_type);
              $single->type = $type;

              $asset = hid_ex_m_get_asset_full_name($single->asset_type, $single->asset_id);
              $single->asset = $asset;

              $single->snapshot = wp_get_attachment_url($single->proof_of_payment);

              $single->status = LuxUtils::lux_get_order_status($single->order_status);

              unset($single->order_status);
              unset($single->id);
              unset($single->customer_id);
              unset($single->quantity_sold);
              unset($single->asset_id);
              unset($single->asset_type);
              unset($single->proof_of_payment);
              unset($single->amount_to_recieve);
            }
          }
        }

        if (empty($output_data)) {
          $output_data = array(
            "message"   => "No history found"
          );
        }

        $response = new WP_REST_Response($output_data);
        $response->set_status(200);

        return $response;
      } else {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for process', // data
          array('status' => 400) // status
        );
      }
    }
  }
}

new LuxRest;
