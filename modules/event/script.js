function initEventCalendar() {
  new Calendar("event-calendar", {
    url: WEB_URL + "index.php/event/model/calendar/toJSON"
  });
}