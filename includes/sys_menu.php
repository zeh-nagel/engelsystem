<?php

use Engelsystem\UserHintsRenderer;

/**
 * @param string $page
 * @param array  $parameters get parameters
 * @return string
 */
function page_link_to($page = '', $parameters = [])
{
    $page = str_replace('_', '-', $page);
    return url($page, $parameters);
}

/**
 * Render the user hints
 *
 * @return string
 */
function header_render_hints()
{
    global $user;

    $hints_renderer = new UserHintsRenderer();

    if (isset($user)) {
        $hints_renderer->addHint(admin_new_questions());
        $hints_renderer->addHint(user_angeltypes_unconfirmed_hint());
        $hints_renderer->addHint(render_user_departure_date_hint());
        $hints_renderer->addHint(user_driver_license_required_hint());

        // Important hints:
        $hints_renderer->addHint(render_user_freeloader_hint(), true);
        $hints_renderer->addHint(render_user_arrived_hint(), true);
        $hints_renderer->addHint(render_user_tshirt_hint(), true);
        $hints_renderer->addHint(render_user_dect_hint(), true);
    }

    return $hints_renderer->render();
}

/**
 * Renders the header toolbar containing search, login/logout, user and settings links.
 *
 * @return string
 */
function header_toolbar()
{
    global $page, $privileges, $user;

    $toolbar_items = [];

    if (isset($user)) {
        $toolbar_items[] = toolbar_item_link(
            page_link_to('shifts', ['action' => 'next']),
            'time',
            User_shift_state_render($user)
        );
    }

    if (!isset($user) && in_array('register', $privileges) && config('registration_enabled')) {
        $toolbar_items[] = toolbar_item_link(
            page_link_to('register'),
            'plus',
            register_title(),
            $page == 'register'
        );
    }

    if (in_array('login', $privileges)) {
        $toolbar_items[] = toolbar_item_link(
            page_link_to('login'),
            'log-in',
            login_title(),
            $page == 'login'
        );
    }

    if (isset($user) && in_array('user_messages', $privileges)) {
        $toolbar_items[] = toolbar_item_link(
            page_link_to('user_messages'),
            'envelope',
            user_unread_messages()
        );
    }

    $toolbar_items[] = header_render_hints();
    if (in_array('user_myshifts', $privileges)) {
        $toolbar_items[] = toolbar_item_link(
            page_link_to('users', ['action' => 'view']),
            '',
            '<span class="icon-icon_angel"></span> ' . $user['Nick'],
            $page == 'users'
        );
    }

    $user_submenu = make_user_submenu();
    if (count($user_submenu) > 0) {
        $toolbar_items[] = toolbar_dropdown('', '', $user_submenu);
    }

    return toolbar($toolbar_items, true);
}

/**
 * @return array
 */
function make_user_submenu()
{
    global $privileges, $page;

    $user_submenu = make_language_select();

    if (in_array('user_settings', $privileges) || in_array('logout', $privileges)) {
        $user_submenu[] = toolbar_item_divider();
    }

    if (in_array('user_settings', $privileges)) {
        $user_submenu[] = toolbar_item_link(
            page_link_to('user_settings'),
            'list-alt',
            settings_title(),
            $page == 'user_settings'
        );
    }

    if (in_array('logout', $privileges)) {
        $user_submenu[] = toolbar_item_link(
            page_link_to('logout'),
            'log-out',
            logout_title(),
            $page == 'logout'
        );
    }

    return $user_submenu;
}

/**
 * @return string
 */
function make_navigation()
{
    global $page, $privileges;

    $menu = [];
    $pages = [
        'news'           => news_title(),
        'user_meetings'  => meetings_title(),
        'user_shifts'    => shifts_title(),
        'angeltypes'     => angeltypes_title(),
        'user_questions' => questions_title()
    ];

    foreach ($pages as $menu_page => $title) {
        if (in_array($menu_page, $privileges)) {
            $menu[] = toolbar_item_link(page_link_to($menu_page), '', $title, $menu_page == $page);
        }
    }

    $menu = make_room_navigation($menu);

    $admin_menu = [];
    $admin_pages = [
        'admin_arrive'       => admin_arrive_title(),
        'admin_active'       => admin_active_title(),
        'admin_user'         => admin_user_title(),
        'admin_free'         => admin_free_title(),
        'admin_questions'    => admin_questions_title(),
        'shifttypes'         => shifttypes_title(),
        'admin_shifts'       => admin_shifts_title(),
        'admin_rooms'        => admin_rooms_title(),
        'admin_groups'       => admin_groups_title(),
        'admin_import'       => admin_import_title(),
        'admin_log'          => admin_log_title(),
        'admin_event_config' => event_config_title()
    ];

    foreach ($admin_pages as $menu_page => $title) {
        if (in_array($menu_page, $privileges)) {
            $admin_menu[] = toolbar_item_link(
                page_link_to($menu_page),
                '',
                $title,
                $menu_page == $page
            );
        }
    }

    if (count($admin_menu) > 0) {
        $menu[] = toolbar_dropdown('', __('Admin'), $admin_menu);
    }

    return toolbar($menu);
}

/**
 * Adds room navigation to the given menu.
 *
 * @param string[] $menu Rendered menu
 * @return string[]
 */
function make_room_navigation($menu)
{
    global $privileges;

    if (!in_array('view_rooms', $privileges)) {
        return $menu;
    }

    // Get a list of all rooms
    $rooms = Rooms();
    $room_menu = [];
    if (in_array('admin_rooms', $privileges)) {
        $room_menu[] = toolbar_item_link(page_link_to('admin_rooms'), 'list', __('Manage rooms'));
    }
    if (count($room_menu) > 0) {
        $room_menu[] = toolbar_item_divider();
    }
    foreach ($rooms as $room) {
        $room_menu[] = toolbar_item_link(room_link($room), 'map-marker', $room['Name']);
    }
    if (count($room_menu) > 0) {
        $menu[] = toolbar_dropdown('map-marker', __('Rooms'), $room_menu);
    }
    return $menu;
}

/**
 * Renders language selection.
 *
 * @return array
 */
function make_language_select()
{
    $request = app('request');
    $activeLocale = session()->get('locale');

    $items = [];
    foreach (config('locales') as $locale => $name) {
        $url = url($request->getPathInfo(), ['set-locale' => $locale]);

        $items[] = toolbar_item_link(
            htmlspecialchars($url),
            '',
            $name,
            $locale == $activeLocale
        );
    }
    return $items;
}

/**
 * @return string
 */
function make_menu()
{
    return make_navigation();
}
