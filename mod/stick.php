<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$TANGO->perm->check('access_moderation')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
$TANGO->tpl->getTpl('page');

$content = '';

if ($PGET->g('thread')) {

    $MYSQL->bind('id', $PGET->g('thread'));
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE id = :id");

    if (!empty($query)) {

        if ($query['0']['post_sticky'] == "0") {
            $MYSQL->bind('id', $PGET->g('thread'));

            if ($MYSQL->query("UPDATE {prefix}forum_posts SET post_sticky = 1 WHERE id = :id") > 0) {
                $content .= $TANGO->tpl->entity(
                    'success_notice',
                    'content',
                    str_replace(
                        '%url%',
                        SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id'],
                        $LANG['mod']['stick']['stick_success']
                    )
                );
            } else {
                $content .= $TANGO->tpl->entity(
                    'danger_notice',
                    'content',
                    $LANG['mod']['stick']['stick_error']
                );
            }

        } else {
            $content .= $TANGO->tpl->entity(
                'danger_notice',
                'content',
                $LANG['mod']['stick']['already_stuck']
            );
        }

    } else {
        redirect(SITE_URL);
    }

} else {
    redirect(SITE_URL);
}

$TANGO->tpl->addParam(
    array(
        'page_title',
        'content'
    ),
    array(
        $LANG['mod']['stick']['stick'],
        $content
    )
);

echo $TANGO->tpl->output();

?>