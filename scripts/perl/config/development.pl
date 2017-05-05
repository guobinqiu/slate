use common::sense;

+{
    is_test => 0,
    database => {
        dsn  => 'dbi:mysql:dbname=jili_db',
        user => 'YOUR_USERNAME',
        pass => 'YOUR_PASSWORD',
    },
    email => {
        from  => 'SHOULD_BE_LOAD_FROM_LOCAL_GIT',
    },
};
