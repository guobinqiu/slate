use common::sense;

+{
    is_test => 0,
    database => {
        dsn  => 'dbi:mysql:dbname=jili_dev',
        user => 'root',
        pass => '',
    },
    email => {
        from  => 'YOUR_EMAIL_ADDR',
    },
    slack_url => 'https://hooks.slack.com/services/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',
};