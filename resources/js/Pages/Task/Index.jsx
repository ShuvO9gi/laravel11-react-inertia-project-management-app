import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import TasksTable from "./TasksTable";
import { useEffect, useState } from "react";

export default function Index({ auth, tasks, success, queryParams = null }) {
  // console.log(success);
  const [showSuccess, setShowSuccess] = useState(false);

  // useEffect(() => {
  //   if (success) {
  //     setShowSuccess(true);
  //     const timer = setTimeout(() => setShowSuccess(false), 5000);
  //     return () => clearTimeout(timer);
  //   }
  // }, [success]);

  // useEffect(() => {
  //   const storedSuccess = sessionStorage.getItem("success");
  //   console.log(storedSuccess);

  //   if (success && !storedSuccess) {
  //     setShowSuccess(true);
  //     sessionStorage.setItem("success", success); // Set flag to indicate message has been shown
  //     console.log("session", sessionStorage.getItem("success"));
  //     const timer = setTimeout(() => {
  //       setShowSuccess(false);
  //       sessionStorage.removeItem(success); // Remove flag after hiding the message
  //       console.log(
  //         "session removed",
  //         sessionStorage.getItem("success", success)
  //       );
  //     }, 5000);

  //     return () => clearTimeout(timer);
  //   } else if (storedSuccess) {
  //     console.log("stored", storedSuccess);
  //     setShowSuccess(true);
  //     const timer = setTimeout(() => {
  //       setShowSuccess(false);
  //       sessionStorage.removeItem("success");
  //     }, 5000);

  //     return () => clearTimeout(timer);
  //   }
  // }, [success]);

  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    if (success) {
      localStorage.setItem("success", success);
      setSuccessMessage(success);
      setShowSuccess(true);

      const timer = setTimeout(() => {
        setShowSuccess(false);
        localStorage.removeItem("success");
      }, 5000);

      return () => clearTimeout(timer);
    } else {
      const storedSuccess = localStorage.getItem("success");
      if (storedSuccess) {
        setSuccessMessage(storedSuccess);
        setShowSuccess(true);

        const timer = setTimeout(() => {
          setShowSuccess(false);
          localStorage.removeItem("success");
        }, 5000);

        return () => clearTimeout(timer);
      }
    }
  }, [success]);

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <div className="flex justify-between items-center">
          <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tasks
          </h2>
          <Link
            href={route("task.create")}
            className="bg-emerald-500 py-1 px-3 text-white rounded shadow transition-all hover:bg-emerald-600"
          >
            Add New
          </Link>
        </div>
      }
    >
      <Head title="Tasks" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {/* {success && (
            <div
              id="flash-message"
              className={`bg-emerald-500 py-2 px-4 text-white rounded mb-4 transition-[opacity] ease-in-out duration-1000 ${
                showSuccess ? "opacity-100" : "opacity-0"
              }`}
            >
              {success}
            </div>
          )} */}
          {showSuccess && (
            <div
              id="flash-message"
              className={`bg-emerald-500 py-2 px-4 text-white rounded mb-4 transition-opacity ease-in-out duration-1000 ${
                showSuccess ? "opacity-100" : "opacity-0"
              }`}
            >
              {successMessage}
            </div>
          )}
          <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900 dark:text-gray-100">
              {/* <pre>{JSON.stringify(tasks, undefined, 2)}</pre> */}
              <TasksTable tasks={tasks} queryParams={queryParams} />
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
