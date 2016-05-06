use common::sense;

+{
    is_test => 0,
    database => {
        dsn  => 'dbi:mysql:dbname=SHOULD_BE_LOAD_FROM_LOCAL_GIT',
        user => 'SHOULD_BE_LOAD_FROM_LOCAL_GIT',
        pass => 'SHOULD_BE_LOAD_FROM_LOCAL_GIT',
    },
        email => {
        from  => 'SHOULD_BE_LOAD_FROM_LOCAL_GIT',
    },
};
