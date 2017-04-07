// Response sends a reply to incoming command requests.
type Response interface {
	// IsRequired returns true if the sender is waiting for the response.
	//
	// If the response is not required, any payload data sent is discarded.
	// The response must always be closed, even if IsRequired() returns false.
	IsRequired() bool

	// IsClosed true if the response has already been closed.
	IsClosed() bool

	// Done sends a payload to the source session and closes the response.
	//
	// A panic occurs if the response has already been closed.
	Done(*Payload)

	// Error sends an error to the source session and closes the response.
	//
	// A panic occurs if the response has already been closed.
	Error(error)

	// Fail is a convenience method that creates a Failure and passes it to
	// Error() method. The created failure is returned.
	//
	// The failure type t is used verbatim. The failure message is formatted
	// according to the format specifier f, interpolated with values from v.
	//
	// A panic occurs if the response has already been closed or if failureType
	// is empty.
	Fail(t, f string, v ...interface{}) Failure

	// Close finalizes the response.
	//
	// If the origin session is expecting response it will receive a nil payload.
	//
	// It is not an error to close a responder multiple times. The return value
	// is true the first time Close() is called, and false on subsequent calls.
	Close() bool
}
