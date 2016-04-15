package Wenwen::Model::Schema;
use common::sense;

use Teng::Schema::Declare;
use DateTime;

table {
    name '91ww_user_token';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'email', type => 12},
        {name => 'token', type => 12},
    );
};

table {
    name 'activity_category';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'category', type => 12},
    );
};

table {
    name 'activity_gathering_taobao_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'order_identity', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'ad_activity';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'title', type => 12},
        {name => 'description', type => 12},
        {name => 'started_at', type => 11},
        {name => 'finished_at', type => 11},
        {name => 'percentage', type => 8},
        {name => 'is_deleted', type => 4},
        {name => 'is_hidden', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'ad_banner';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'create_time', type => 11},
        {name => 'icon_image', type => 12},
        {name => 'position', type => 4},
        {name => 'ad_url', type => 12},
    );
};

table {
    name 'ad_category';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'category_name', type => 12},
        {name => 'asp', type => 12},
        {name => 'display_name', type => 12},
    );
};

table {
    name 'ad_position';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'type', type => 12},
        {name => 'position', type => 4},
        {name => 'ad_id', type => 4},
    );
};

table {
    name 'advertiserment';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'type', type => 12},
        {name => 'title', type => 12},
        {name => 'action_id', type => 4},
        {name => 'created_time', type => 11},
        {name => 'start_time', type => 11},
        {name => 'end_time', type => 11},
        {name => 'update_time', type => 11},
        {name => 'decription', type => 12},
        {name => 'content', type => 12},
        {name => 'imageurl', type => 12},
        {name => 'is_expired', type => 4},
        {name => 'icon_image', type => 12},
        {name => 'list_image', type => 12},
        {name => 'incentive_type', type => 4},
        {name => 'incentive_rate', type => 4},
        {name => 'reward_rate', type => 8},
        {name => 'incentive', type => 4},
        {name => 'info', type => 12},
        {name => 'category', type => 4},
        {name => 'delete_flag', type => 4},
        {name => 'wenwen_user', type => 12},
    );
};

table {
    name 'adw_access_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'access_time', type => 11},
    );
};

table {
    name 'adw_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'create_time', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'adw_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'create_time', type => 11},
        {name => 'happen_time', type => 11},
        {name => 'adw_return_time', type => 11},
        {name => 'confirm_time', type => 11},
        {name => 'incentive_type', type => 4},
        {name => 'incentive', type => 4},
        {name => 'incentive_rate', type => 4},
        {name => 'comm', type => 8},
        {name => 'ocd', type => 12},
        {name => 'order_price', type => 4},
        {name => 'order_status', type => 4},
        {name => 'delete_flag', type => 4},
        {name => 'order_type', type => 4},
    );
};

table {
    name 'amazon_coupon';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'coupon_od', type => 12},
        {name => 'coupon_elec', type => 12},
    );
};

table {
    name 'bangwoya_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'bangwoya_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'tid', type => 12},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'black_users';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'blacked_date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'callboard';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'title', type => 12},
        {name => 'author', type => 12},
        {name => 'content', type => 12},
        {name => 'create_time', type => 11},
        {name => 'update_time', type => 11},
        {name => 'start_time', type => 11},
        {name => 'end_time', type => 11},
        {name => 'cb_type', type => 4},
        {name => 'url', type => 12},
    );
};

table {
    name 'cb_category';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'category_name', type => 12},
    );
};

table {
    name 'chanet_advertisement';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'ads_name', type => 12},
        {name => 'category', type => 12},
        {name => 'ads_url_type', type => 12},
        {name => 'ads_url', type => 12},
        {name => 'marketing_url', type => 12},
        {name => 'selected_at', type => 11},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
    );
};

table {
    name 'chanet_commission';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'chanet_commission_data';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'commission_id', type => 4},
        {name => 'commission_serial_number', type => 4},
        {name => 'commission_name', type => 12},
        {name => 'commission', type => 12},
        {name => 'commission_period', type => 12},
        {name => 'description', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'checkin_adver_list';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'inter_space', type => 4},
        {name => 'operation_method', type => 4},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'checkin_auto_shop';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'checkin_adver_list_id', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'checkin_click_list';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'click_date', type => 12},
        {name => 'open_shop_times', type => 4},
        {name => 'status', type => 4},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'checkin_point_times';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'point_times', type => 4},
        {name => 'start_time', type => 11},
        {name => 'end_time', type => 11},
        {name => 'checkin_type', type => 4},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'checkin_user_list';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'click_date', type => 12},
        {name => 'open_shop_id', type => 4},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'cint_permission';
    pk 'user_id';
    columns (
        {name => 'user_id', type => 4},
        {name => 'permission_flag', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'cint_research_survey_participation_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'cint_project_id', type => 4},
        {name => 'cint_project_quota_id', type => 4},
        {name => 'app_member_id', type => 12},
        {name => 'point', type => 4},
        {name => 'type', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'cint_user_agreement_participation_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'agreement_status', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'cityList';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'cityName', type => 12},
        {name => 'provinceId', type => 4},
    );
};

table {
    name 'cps_advertisement';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ad_category_id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'title', type => 12},
        {name => 'marketing_url', type => 12},
        {name => 'ads_url', type => 12},
        {name => 'commission', type => 12},
        {name => 'website_name', type => 12},
        {name => 'website_name_dictionary_key', type => 12},
        {name => 'website_category', type => 12},
        {name => 'website_host', type => 12},
        {name => 'selected_at', type => 11},
        {name => 'is_activated', type => 4},
    );
};

table {
    name 'duomai_advertisement';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'ads_name', type => 12},
        {name => 'ads_url', type => 12},
        {name => 'ads_commission', type => 12},
        {name => 'start_time', type => 9},
        {name => 'end_time', type => 9},
        {name => 'category', type => 12},
        {name => 'return_day', type => 4},
        {name => 'billing_cycle', type => 12},
        {name => 'link_custom', type => 12},
        {name => 'selected_at', type => 11},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
    );
};

table {
    name 'duomai_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'duomai_commission';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'duomai_commission_data';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'commission_id', type => 4},
        {name => 'commission_serial_number', type => 4},
        {name => 'commission_name', type => 12},
        {name => 'commission', type => 12},
        {name => 'commission_period', type => 12},
        {name => 'description', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'duomai_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'ocd', type => 12},
        {name => 'ads_id', type => 4},
        {name => 'ads_name', type => 12},
        {name => 'site_id', type => 4},
        {name => 'link_id', type => 4},
        {name => 'order_sn', type => 12},
        {name => 'order_time', type => 11},
        {name => 'orders_price', type => 8},
        {name => 'comm', type => 8},
        {name => 'status', type => 4},
        {name => 'deactivated_at', type => 11},
        {name => 'confirmed_at', type => 11},
        {name => 'balanced_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'emar_access_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'access_time', type => 11},
    );
};

table {
    name 'emar_activity_commission';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'activity_id', type => 4},
        {name => 'activity_name', type => 12},
        {name => 'activity_category', type => 12},
        {name => 'commission_id', type => 4},
        {name => 'commission_number', type => 12},
        {name => 'commission_name', type => 12},
        {name => 'commission', type => 12},
        {name => 'commission_period', type => 12},
        {name => 'apply_products', type => 12},
        {name => 'description', type => 12},
        {name => 'mall_name', type => 12},
        {name => 'rebate_type', type => 4},
        {name => 'rebate', type => 12},
    );
};

table {
    name 'emar_advertisement';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'ads_name', type => 12},
        {name => 'category', type => 12},
        {name => 'commission', type => 12},
        {name => 'commission_period', type => 12},
        {name => 'ads_url', type => 12},
        {name => 'can_customize_target', type => 4},
        {name => 'feedback_tag', type => 12},
        {name => 'marketing_url', type => 12},
        {name => 'selected_at', type => 11},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
    );
};

table {
    name 'emar_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'emar_commission';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ads_id', type => 4},
        {name => 'fixed_hash', type => 12},
        {name => 'is_activated', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'emar_commission_data';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'commission_id', type => 4},
        {name => 'commission_serial_number', type => 4},
        {name => 'commission_name', type => 12},
        {name => 'commission', type => 12},
        {name => 'commission_period', type => 12},
        {name => 'product_apply_to', type => 12},
        {name => 'description', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'emar_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'ad_type', type => 12},
        {name => 'created_at', type => 11},
        {name => 'returned_at', type => 11},
        {name => 'confirmed_at', type => 11},
        {name => 'happened_at', type => 11},
        {name => 'comm', type => 8},
        {name => 'ocd', type => 12},
        {name => 'status', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'emar_products_cron';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'pid', type => 4},
        {name => 'p_name', type => 12},
        {name => 'web_id', type => 4},
        {name => 'web_name', type => 12},
        {name => 'ori_price', type => 12},
        {name => 'cur_price', type => 12},
        {name => 'pic_url', type => 12},
        {name => 'catid', type => 4},
        {name => 'cname', type => 12},
        {name => 'p_o_url', type => 12},
        {name => 'short_intro', type => 12},
    );
};

table {
    name 'emar_products_croned';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'pid', type => 4},
        {name => 'p_name', type => 12},
        {name => 'web_id', type => 4},
        {name => 'web_name', type => 12},
        {name => 'ori_price', type => 12},
        {name => 'cur_price', type => 12},
        {name => 'pic_url', type => 12},
        {name => 'catid', type => 4},
        {name => 'cname', type => 12},
        {name => 'p_o_url', type => 12},
        {name => 'short_intro', type => 12},
    );
};

table {
    name 'emar_request';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'tag', type => 12},
        {name => 'count', type => 4},
        {name => 'size_up', type => 4},
        {name => 'size_down', type => 4},
        {name => 'time_consumed_total', type => 3},
    );
};

table {
    name 'emar_websites';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'web_id', type => 4},
        {name => 'web_catid', type => 4},
        {name => 'commission', type => 12},
        {name => 'is_deleted', type => 4},
        {name => 'position', type => 4},
        {name => 'is_hidden', type => 4},
        {name => 'is_hot', type => 4},
        {name => 'hot_at', type => 11},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'emar_websites_category';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'web_id', type => 4},
        {name => 'category_id', type => 4},
        {name => 'count', type => 4},
    );
};

table {
    name 'emar_websites_category_cron';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'web_id', type => 4},
        {name => 'category_id', type => 4},
        {name => 'count', type => 4},
    );
};

table {
    name 'emar_websites_cron';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'web_id', type => 4},
        {name => 'web_name', type => 12},
        {name => 'web_catid', type => 4},
        {name => 'logo_url', type => 12},
        {name => 'web_url', type => 12},
        {name => 'information', type => 12},
        {name => 'begin_date', type => 12},
        {name => 'end_date', type => 12},
        {name => 'commission', type => 12},
    );
};

table {
    name 'emar_websites_croned';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'web_id', type => 4},
        {name => 'web_name', type => 12},
        {name => 'web_catid', type => 4},
        {name => 'logo_url', type => 12},
        {name => 'web_url', type => 12},
        {name => 'information', type => 12},
        {name => 'begin_date', type => 12},
        {name => 'end_date', type => 12},
        {name => 'commission', type => 12},
    );
};

table {
    name 'exchange_amazon_result';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'exchange_id', type => 4},
        {name => 'amazonCard_one', type => 12},
        {name => 'amazonCard_two', type => 12},
        {name => 'amazonCard_three', type => 12},
        {name => 'amazonCard_four', type => 12},
        {name => 'amazonCard_five', type => 12},
        {name => 'createtime', type => 11},
    );
};

table {
    name 'exchange_danger';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'exchange_id', type => 4},
        {name => 'danger_type', type => 4},
        {name => 'danger_content', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'exchange_flow_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'exchange_id', type => 4},
        {name => 'provider', type => 12},
        {name => 'province', type => 12},
        {name => 'custom_product_id', type => 12},
        {name => 'packagesize', type => 12},
        {name => 'custom_prise', type => 3},
        {name => 'created_at', type => 11},
        {name => 'updated_at', type => 11},
    );
};

table {
    name 'exchange_from_wenwen';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'wenwen_exchange_id', type => 12},
        {name => 'user_id', type => 4},
        {name => 'email', type => 12},
        {name => 'user_wenwen_cross_id', type => 4},
        {name => 'payment_point', type => 4},
        {name => 'status', type => 4},
        {name => 'reason', type => 12},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'experience_advertisement';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'mission_hall', type => 4},
        {name => 'point', type => 4},
        {name => 'mission_img_url', type => 12},
        {name => 'mission_title', type => 12},
        {name => 'delete_flag', type => 4},
        {name => 'create_time', type => 11},
        {name => 'update_time', type => 11},
    );
};

table {
    name 'flow_order_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'fulcrum_research_survey_participation_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'fulcrum_project_id', type => 4},
        {name => 'fulcrum_project_quota_id', type => 4},
        {name => 'app_member_id', type => 12},
        {name => 'point', type => 4},
        {name => 'type', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'fulcrum_user_agreement_participation_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'app_member_id', type => 12},
        {name => 'agreement_status', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'game_eggs_breaker_eggs_info';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'total_paid', type => 8},
        {name => 'offcut_for_next', type => 8},
        {name => 'num_of_common', type => 4},
        {name => 'num_of_consolation', type => 4},
        {name => 'num_updated_at', type => 11},
        {name => 'token', type => 12},
        {name => 'token_updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'game_eggs_breaker_taobao_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'order_id', type => 12},
        {name => 'order_at', type => 9},
        {name => 'order_paid', type => 8},
        {name => 'audit_by', type => 12},
        {name => 'audit_status', type => 4},
        {name => 'audit_pended_at', type => 11},
        {name => 'is_valid', type => 4},
        {name => 'is_egged', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'game_eggs_broken_log';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'egg_type', type => 4},
        {name => 'points_acquired', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'game_log';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'point_uid', type => 4},
        {name => 'game_point', type => 4},
        {name => 'game_date', type => 12},
        {name => 'game_time', type => 12},
        {name => 'game_score', type => 4},
        {name => 'game_type', type => 4},
        {name => 'mass_point', type => 4},
        {name => 'goal_point', type => 4},
        {name => 'ranking_point', type => 4},
        {name => 'attendance_point', type => 4},
    );
};

table {
    name 'game_seeker_daily';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'points', type => 4},
        {name => 'clicked_day', type => 9},
        {name => 'token', type => 12},
        {name => 'token_updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'game_seeker_points_pool';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'points', type => 4},
        {name => 'send_frequency', type => 4},
        {name => 'is_published', type => 4},
        {name => 'published_at', type => 11},
        {name => 'is_valid', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'hobby_list';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'hobby_name', type => 12},
    );
};

table {
    name 'identity_confirm';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'identity_card', type => 12},
        {name => 'identity_validate_time', type => 11},
    );
};

table {
    name 'is_read_callboard';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'send_cb_id', type => 4},
        {name => 'user_id', type => 4},
    );
};

table {
    name 'jms_cron_jobs';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'command', type => 12},
        {name => 'lastRunAt', type => 11},
    );
};

table {
    name 'jms_job_dependencies';
    pk 'source_job_id','dest_job_id';
    columns (
        {name => 'source_job_id', type => 4},
        {name => 'dest_job_id', type => 4},
    );
};

table {
    name 'jms_job_related_entities';
    pk 'job_id','related_class','related_id';
    columns (
        {name => 'job_id', type => 4},
        {name => 'related_class', type => 12},
        {name => 'related_id', type => 12},
    );
};

table {
    name 'jms_job_statistics';
    pk 'job_id','characteristic','createdAt';
    columns (
        {name => 'job_id', type => 4},
        {name => 'characteristic', type => 12},
        {name => 'createdAt', type => 11},
        {name => 'charValue', type => 8},
    );
};

table {
    name 'jms_jobs';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'state', type => 12},
        {name => 'queue', type => 12},
        {name => 'priority', type => 4},
        {name => 'createdAt', type => 11},
        {name => 'startedAt', type => 11},
        {name => 'checkedAt', type => 11},
        {name => 'workerName', type => 12},
        {name => 'executeAfter', type => 11},
        {name => 'closedAt', type => 11},
        {name => 'command', type => 12},
        {name => 'args', type => 12},
        {name => 'output', type => 12},
        {name => 'errorOutput', type => 12},
        {name => 'exitCode', type => 4},
        {name => 'maxRuntime', type => 4},
        {name => 'maxRetries', type => 4},
        {name => 'stackTrace', type => 12},
        {name => 'runtime', type => 4},
        {name => 'memoryUsage', type => 4},
        {name => 'memoryUsageReal', type => 4},
        {name => 'originalJob_id', type => 4},
    );
};

table {
    name 'kpi_daily_RR';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'kpi_YMD', type => 12},
        {name => 'register_YMD', type => 12},
        {name => 'RR_day', type => 4},
        {name => 'register_user', type => 4},
        {name => 'active_user', type => 4},
        {name => 'RR', type => 4},
    );
};

table {
    name 'limit_ad';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'income', type => 4},
        {name => 'incentive', type => 4},
    );
};

table {
    name 'limit_ad_result';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'adw_order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'limit_ad_id', type => 4},
        {name => 'result_incentive', type => 4},
    );
};

table {
    name 'login_log';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'login_date', type => 11},
        {name => 'login_ip', type => 12},
    );
};

table {
    name 'market_activity';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'aid', type => 4},
        {name => 'business_name', type => 12},
        {name => 'activity_description', type => 12},
        {name => 'category_id', type => 12},
        {name => 'activity_url', type => 12},
        {name => 'activity_image', type => 12},
        {name => 'start_time', type => 11},
        {name => 'end_time', type => 11},
        {name => 'create_time', type => 11},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'market_activity_click_number';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'click_number', type => 4},
        {name => 'market_activity_id', type => 4},
    );
};

table {
    name 'month_income';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'income', type => 12},
    );
};

table {
    name 'offer99_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'offer99_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'tid', type => 12},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'offerwow_api_return';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'content', type => 12},
    );
};

table {
    name 'offerwow_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'created_at', type => 11},
        {name => 'returned_at', type => 11},
        {name => 'confirmed_at', type => 11},
        {name => 'happened_at', type => 11},
        {name => 'eventid', type => 12},
        {name => 'status', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'pag_order';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'session_id', type => 12},
        {name => 'point_uid', type => 4},
        {name => 'point_pid', type => 12},
        {name => 'date', type => 12},
        {name => 'date2', type => 12},
        {name => 'price', type => 8},
        {name => 'status', type => 4},
        {name => 'amounts', type => 8},
        {name => 'point', type => 4},
    );
};

table {
    name 'point_history00';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 4},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history01';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history02';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history03';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history04';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history05';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history06';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history07';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history08';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_history09';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'point_change_num', type => 12},
        {name => 'reason', type => 4},
        {name => 'create_time', type => 11},
    );
    inflate create_time => sub {
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate create_time => sub {
        my ($col_value) = @_;
        $col_value->epoch;
    };
};

table {
    name 'point_reason';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'reason', type => 12},
    );
};

table {
    name 'points_exchange';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'exchange_date', type => 11},
        {name => 'finish_date', type => 11},
        {name => 'type', type => 4},
        {name => 'target_account', type => 12},
        {name => 'real_name', type => 12},
        {name => 'source_point', type => 4},
        {name => 'target_point', type => 4},
        {name => 'exchange_item_number', type => 4},
        {name => 'status', type => 4},
        {name => 'ip', type => 12},
    );
};

table {
    name 'points_exchange_type';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'type', type => 12},
    );
};

table {
    name 'provinceList';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'provinceName', type => 12},
    );
};

table {
    name 'qq_user';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'open_id', type => 12},
    );
};

table {
    name 'rate_ad';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ad_id', type => 4},
        {name => 'income_rate', type => 4},
        {name => 'incentive_rate', type => 4},
    );
};

table {
    name 'rate_ad_result';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'adw_order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'rate_ad_id', type => 4},
        {name => 'result_price', type => 4},
        {name => 'result_incentive', type => 4},
    );
};

table {
    name 'register_reward';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'type', type => 4},
        {name => 'rewards', type => 4},
    );
};

table {
    name 'send_callboard';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message00';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message01';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message02';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message03';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message04';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message05';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message06';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message07';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message08';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_message09';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'sendFrom', type => 4},
        {name => 'sendTo', type => 4},
        {name => 'title', type => 12},
        {name => 'content', type => 12},
        {name => 'createtime', type => 11},
        {name => 'read_flag', type => 4},
        {name => 'delete_flag', type => 4},
    );
};

table {
    name 'send_point_fail';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'send_type', type => 4},
        {name => 'create_time', type => 11},
    );
};

table {
    name 'set_password_code';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'code', type => 12},
        {name => 'create_time', type => 11},
        {name => 'is_available', type => 4},
    );
};

table {
    name 'sop_profile_point';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'name', type => 12},
        {name => 'point_value', type => 4},
        {name => 'hash', type => 12},
        {name => 'status_flag', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'sop_research_survey_participation_history';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'partner_app_project_id', type => 4},
        {name => 'partner_app_project_quota_id', type => 4},
        {name => 'app_member_id', type => 12},
        {name => 'point', type => 4},
        {name => 'type', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'sop_respondent';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'status_flag', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'ssi_project';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'status_flag', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'ssi_project_respondent';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'ssi_project_id', type => 4},
        {name => 'ssi_respondent_id', type => 4},
        {name => 'ssi_mail_batch_id', type => 4},
        {name => 'start_url_id', type => 12},
        {name => 'answer_status', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'completed_at', type => 11},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'ssi_respondent';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'status_flag', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'taobao_category';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'category_name', type => 12},
        {name => 'union_product', type => 4},
        {name => 'delete_flag', type => 4},
        {name => 'created_at', type => 11},
        {name => 'updated_at', type => 11},
    );
};

table {
    name 'taobao_component';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'component_id', type => 4},
        {name => 'category_id', type => 4},
        {name => 'keyword', type => 12},
        {name => 'content', type => 12},
        {name => 'sort', type => 4},
        {name => 'created_at', type => 11},
        {name => 'updated_at', type => 11},
    );
};

table {
    name 'taobao_recommend';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'component_ids', type => 12},
        {name => 'recommend_name', type => 12},
        {name => 'created_at', type => 11},
        {name => 'updated_at', type => 11},
    );
};

table {
    name 'taobao_self_promotion_products';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'taobao_category_id', type => 4},
        {name => 'title', type => 12},
        {name => 'price', type => 8},
        {name => 'price_promotion', type => 8},
        {name => 'item_url', type => 12},
        {name => 'click_url', type => 12},
        {name => 'picture_name', type => 12},
        {name => 'comment_description', type => 12},
        {name => 'promotion_rate', type => 8},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'task_history00';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history01';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history02';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history03';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history04';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history05';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history06';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history07';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history08';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'task_history09';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'order_id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'task_type', type => 4},
        {name => 'category_type', type => 4},
        {name => 'task_name', type => 12},
        {name => 'reward_percent', type => 8},
        {name => 'point', type => 4},
        {name => 'ocd_created_date', type => 11},
        {name => 'date', type => 11},
        {name => 'status', type => 4},
    );
};

table {
    name 'user';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'is_from_wenwen', type => 4},
        {name => 'wenwen_user', type => 12},
        {name => 'token', type => 12},
        {name => 'nick', type => 12},
        {name => 'pwd', type => 12},
        {name => 'sex', type => 4},
        {name => 'birthday', type => 12},
        {name => 'email', type => 12},
        {name => 'is_email_confirmed', type => 4},
        {name => 'tel', type => 12},
        {name => 'is_tel_confirmed', type => 4},
        {name => 'province', type => 4},
        {name => 'city', type => 4},
        {name => 'education', type => 4},
        {name => 'profession', type => 4},
        {name => 'income', type => 4},
        {name => 'hobby', type => 12},
        {name => 'personalDes', type => 12},
        {name => 'identity_num', type => 12},
        {name => 'reward_multiple', type => 8},
        {name => 'register_date', type => 11},
        {name => 'last_login_date', type => 11},
        {name => 'last_login_ip', type => 12},
        {name => 'points', type => 4},
        {name => 'delete_flag', type => 4},
        {name => 'delete_date', type => 11},
        {name => 'is_info_set', type => 4},
        {name => 'icon_path', type => 12},
        {name => 'uniqkey', type => 12},
        {name => 'token_created_at', type => 11},
        {name => 'origin_flag', type => 4},
        {name => 'created_remote_addr', type => 12},
        {name => 'created_user_agent', type => 12},
        {name => 'campaign_code', type => 12},
        {name => 'password_choice', type => 4},
        {name => 'fav_music', type => 12},
        {name => 'monthly_wish', type => 12},
        {name => 'industry_code', type => 4},
        {name => 'work_section_code', type => 4},
        {name => 'register_complete_date', type => 11}, 
    );
    inflate register_complete_date => sub{
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate register_complete_date => sub{
        my ($col_value) = @_;
        $col_value->epoch;    
    };
    inflate register_date => sub{
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate register_date => sub{
        my ($col_value) = @_;
        $col_value->epoch;    
    };
    inflate delete_date => sub{
        my ($col_value) = @_;
        DateTime->from_epoch($col_value);
    };
    deflate delete_date => sub{
        my ($col_value) = @_;
        $col_value->epoch;    
    };
};

table {
    name 'user_91ww_visit';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 12},
    );
};

table {
    name 'user_advertiserment_visit';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 12},
    );
};

table {
    name 'user_configurations';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'flag_name', type => 12},
        {name => 'flag_data', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
        {name => 'user_id', type => 4},
    );
};

table {
    name 'user_edm_unsubscribe';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'created_time', type => 11},
    );
};

table {
    name 'user_game_visit';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 12},
    );
};

table {
    name 'user_info_visit';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 12},
    );
};

table {
    name 'user_sign_up_route';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'source_route', type => 12},
        {name => 'created_time', type => 11},
    );
};

table {
    name 'user_taobao_visit';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 12},
    );
};

table {
    name 'user_visit_log';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'target_flag', type => 4},
        {name => 'user_id', type => 4},
        {name => 'visit_date', type => 9},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'user_wenwen_cross';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'user_wenwen_cross_token';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'cross_id', type => 4},
        {name => 'token', type => 12},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'user_wenwen_login';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'login_password_salt', type => 12},
        {name => 'login_password_crypt_type', type => 12},
        {name => 'login_password', type => 12},
    );
};

table {
    name 'vote';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'title', type => 12},
        {name => 'description', type => 12},
        {name => 'start_time', type => 11},
        {name => 'end_time', type => 11},
        {name => 'point_value', type => 4},
        {name => 'stash_data', type => 12},
        {name => 'vote_image', type => 12},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'vote_answer';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'vote_id', type => 4},
        {name => 'answer_number', type => 4},
        {name => 'updated_at', type => 11},
        {name => 'created_at', type => 11},
    );
};

table {
    name 'weibo_user';
    pk 'id';
    columns (
        {name => 'id', type => 4},
        {name => 'user_id', type => 4},
        {name => 'open_id', type => 12},
        {name => 'regist_date', type => 11},
    );
};

1;
