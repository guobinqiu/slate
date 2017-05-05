use common::sense;

+{
    is_test => 0,
    database => {
        dsn  => 'dbi:mysql:dbname=jili_db',
        user => 'YOUR_USERNAME',
        pass => 'YOUR_PASSWORD',
    },
    email => {
        from  => 'YOUR_EMAIL_ADDR',
    },
};
