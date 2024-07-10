export default function TableHeading({
  name,
  sort_field = null,
  sort_direction = null,
  children,
}) {
  return (
    <th onClick={(e) => sortChanged(name)}>
      <div className="px-3 py-3 flex items-center justify-between gap-1 cursor-pointer">
        {children}
        <div>
          <ChevronUpIcon
            className={
              "w-4 " +
              (sort_field === name && sort_direction === "asc"
                ? "text-white"
                : "")
            }
          />
          <ChevronDownIcon
            className={
              "w-4 -mt-2 " +
              (sort_field === "id" && sort_direction === "desc"
                ? "text-white"
                : "")
            }
          />
        </div>
      </div>
    </th>
  );
}
