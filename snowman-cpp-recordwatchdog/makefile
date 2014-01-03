#Bernard Ladenthin
#2013-07-13
VERSION = 0.1
CC      = /usr/bin/g++
CFLAGS  = --std=c++0x -Wall -Wextra -pedantic-errors -ggdb -DVERSION=\"$(VERSION)\" -D _POSIX_C_SOURCE=200809L
LDFLAGS = -lboost_system -lboost_thread -lboost_program_options -lm -pthread

OBJ = snowman-cpp-recordwatchdog.o
prefix=/usr

snowman-cpp-recordwatchdog: $(OBJ)
	$(CC) $(CFLAGS) -o snowman-cpp-recordwatchdog $(OBJ) $(LDFLAGS)

%.o: %.c
	$(CC) $(CFLAGS) -c $<
run:
	./snowman-cpp-recordwatchdog
runvalgrind:
	valgrind --tool=memcheck --leak-check=full --show-reachable=yes ./snowman-cpp-recordwatchdog
rungdb:
	gdb ./snowman-cpp-recordwatchdog
clean:
	rm *.o
	rm snowman-cpp-recordwatchdog

install: snowman-cpp-recordwatchdog
	install -m 0755 snowman-cpp-recordwatchdog $(prefix)/bin
	install -m 0755 snowman-cpp-recordwatchdog $(prefix)/local/bin

.PHONY: clean install

