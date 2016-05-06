package Wenwen::Model::Schema;
use common::sense;

use Teng::Schema::Declare;
use Wenwen::Util qw(inflate_timestamp deflate_timestamp);

table {
    name 'user';
    pk 'id';
    columns qw(
        id
        is_from_wenwen
        wenwen_user
        token
        nick
        pwd
        sex
        birthday
        email
        is_email_confirmed
        tel
        is_tel_confirmed
        province
        city
        education
        profession
        income
        hobby
        personalDes
        identity_num
        reward_multiple
        register_date
        register_complete_date
        last_login_date
        last_login_ip
        points
        delete_flag
        delete_date
        is_info_set
        icon_path
        uniqkey
        token_created_at
        origin_flag
        created_remote_addr
        created_user_agent
        campaign_code
        password_choice
        fav_music
        monthly_wish
        industry_code
        work_section_code
    );

    inflate qr/_date\z/ => \&inflate_timestamp;
    deflate qr/_date\z/ => \&deflate_timestamp;

};

# point_history00 .. point_history09
table {
    name sprintf("point_history%02d", $_);
    pk "id";
    columns qw(
        id
        user_id
        point_change_num
        reason
        create_time
    );

    deflate qr/_time\z/ => \&deflate_timestamp;
}
for 0 .. 9;

table {
    name 'user_sign_up_route';
    pk 'id';
    columns qw(
        id
        user_id
        source_route
        created_time
    );
};

table {
    name 'user_withdraw';
    pk 'id';
    columns qw(
        id
        user_id
        reason
        created_at
    );
    inflate created_at => \&inflate_timestamp;
    deflate created_at => \&deflate_timestamp;
};

1;
