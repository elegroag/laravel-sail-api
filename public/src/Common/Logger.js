export default class Logger {
	static #instance;
	#logs = [];
	#maxLogs = 1000;

	constructor() {
		if (Logger.#instance) {
			return Logger.#instance;
		}
		Logger.#instance = this;
	}

	log(level, message, data = null) {
		const logEntry = {
			timestamp: new Date().toISOString(),
			level,
			message,
			data: data ? this.structuredClone(data) : null,
		};

		this.#logs.push(logEntry);

		// Mantener solo los últimos logs
		if (this.#logs.length > this.#maxLogs) {
			this.#logs = this.#logs.slice(-this.#maxLogs);
		}

		console.log(message, data);
	}

	info(message, data) {
		this.log('info', message, data);
	}
	warn(message, data) {
		this.log('warn', message, data);
	}
	error(message, data) {
		this.log('error', message, data);
	}
	debug(message, data) {
		this.log('debug', message, data);
	}

	getLogs(level = null) {
		return level ? this.#logs.filter((log) => log.level === level) : [...this.#logs];
	}

	clear() {
		this.#logs = [];
	}

	structuredClone(data) {
		return JSON.parse(JSON.stringify(data));
	}
}
