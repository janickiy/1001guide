export const fieldsToShow = [

  {
    name: "id",
    type: "hidden"
  },

  {
    name: "name",
    title: "Название",
    type: "text"
  },
  {
    name: "type",
    title: "Тип страницы",
    type: "select",
    variants: [
      {
        name: "location",
        value: "Локация"
      },
      {
        name: "country",
        value: "Страна"
      }
    ]
  },
  {
    name: "field",
    title: "Поле",
    type: "text"
  },

];
